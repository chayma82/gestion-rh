<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FactureVente;
use App\Models\PaiementFactureVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
class FactureVenteController extends Controller
{
    /** Messages de validation en français, réutilisés dans store() et update() */
    protected function messagesFacture(): array
    {
        return [
            'date_echeance.after_or_equal' => 'La date d\'échéance doit être une date postérieure ou égale à la date d\'émission de la facture.',
        ];
    }

    public function index(Request $request)
    {
        $entrepriseId = current_entreprise_id();

        $factures = FactureVente::actives()
            ->where('entreprise_id', $entrepriseId)
            ->when($request->q, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('numFacture', 'like', '%' . $request->q . '%')
                      ->orWhere('nom_client', 'like', '%' . $request->q . '%');
                });
            })
            ->when($request->mois, function ($query) use ($request) {
                $query->whereMonth('dateEmissionFacture', $request->mois);
            })
            ->latest('dateEmissionFacture')
            ->paginate(15)
            ->withQueryString();

        $base = FactureVente::actives()->where('entreprise_id', $entrepriseId);

        $totalFactures     = (clone $base)->count();
        $facturesPayees    = (clone $base)->where('statut', 'payee')->count();
        $facturesEnRetard  = (clone $base)->where('statut', 'en_retard')->count();
        $montantTotalTtc   = (clone $base)->sum('montant_ttc');

        return view('factures.ventes.liste', compact(
            'factures', 'totalFactures', 'facturesPayees', 'facturesEnRetard', 'montantTotalTtc'
        ));
    }

    public function archives()
    {
        $factures = FactureVente::archivees()
            ->where('entreprise_id', current_entreprise_id())
            ->latest('updated_at')
            ->paginate(15);

        return view('factures.ventes.archives', compact('factures'));
    }

    public function create()
    {
        $clients = Client::where('entreprise_id', current_entreprise_id())
            ->where('status', 'actif')
            ->orderBy('nom')
            ->get();

        return view('factures.ventes.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'                 => 'required|exists:clients,id',
            'dateEmissionFacture'       => 'required|date',
            'date_echeance'             => 'required|date|after_or_equal:dateEmissionFacture',
            'pdf_facture'               => 'nullable|file|mimes:pdf|max:10240',
            'lignes'                    => 'required|array|min:1',
            'lignes.*.reference'        => 'nullable|string|max:100',
            'lignes.*.designation'      => 'required|string|max:255',
            'lignes.*.description'      => 'nullable|string|max:255',
            'lignes.*.quantite'         => 'required|numeric|min:0.01',
            'lignes.*.unite'            => 'nullable|string|max:50',
            'lignes.*.prix_unitaire'    => 'required|numeric|min:0',
            'lignes.*.taux_tva'         => 'required|numeric|min:0',
        ], $this->messagesFacture());

        DB::transaction(function () use ($data, $request) {
            $client = Client::findOrFail($data['client_id']);
            $entrepriseId = current_entreprise_id();

            $montantHt  = 0;
            $montantTva = 0;
            foreach ($data['lignes'] as $ligne) {
                $montantLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $montantHt  += $montantLigne;
                $montantTva += $montantLigne * ($ligne['taux_tva'] / 100);
            }

            $dernierNumero = FactureVente::where('entreprise_id', $entrepriseId)->count() + 1;

            $facture = FactureVente::create([
                'tenant_id'           => current_tenant_id(),
                'entreprise_id'       => $entrepriseId,
                'client_id'           => $client->id,
                'nom_client'          => $client->nom,
                'numFacture'          => 'FV-' . now()->year . '-' . str_pad($dernierNumero, 4, '0', STR_PAD_LEFT),
                'dateEmissionFacture' => $data['dateEmissionFacture'],
                'date_echeance'       => $data['date_echeance'],
                'montant_ht'          => $montantHt,
                'montant_tva'         => $montantTva,
                'montant_ttc'         => $montantHt + $montantTva,
                'montant_paye'        => 0,
                'montant_restant'     => $montantHt + $montantTva,
                'statut'              => 'en_attente',
            ]);

            if ($request->hasFile('pdf_facture')) {
                $fichier = $request->file('pdf_facture');
                $nomOriginal = $fichier->getClientOriginalName();

                // Dossier dédié à cette facture : factures/ventes/{facture_id}/facture.pdf
                $chemin = $fichier->storeAs(
                    'factures/ventes/' . $facture->id,
                    'facture.' . $fichier->getClientOriginalExtension(),
                    'public'
                );

                $facture->update([
                    'chemin_pdf' => $chemin,
                    'nom_pdf'    => $nomOriginal,
                ]);
            }

            foreach ($data['lignes'] as $ligne) {
                $montantLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $facture->details()->create([
                    'reference'     => $ligne['reference'] ?? null,
                    'designation'   => $ligne['designation'],
                    'description'   => $ligne['description'] ?? '',
                    'quantite'      => $ligne['quantite'],
                    'unite'         => $ligne['unite'] ?? null,
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'taux_tva'      => $ligne['taux_tva'],
                    'montant_ligne' => $montantLigne,
                ]);
            }
        });

        return redirect()->route('factures.ventes.index')->with('success', 'Facture de vente créée avec succès.');
    }

    public function info(FactureVente $facture)
    {
        return view('factures.ventes.info', compact('facture'));
    }

    public function edit(FactureVente $facture)
    {
        $clients = Client::where('entreprise_id', current_entreprise_id())
            ->where('status', 'actif')
            ->orderBy('nom')
            ->get();

        return view('factures.ventes.edit', compact('facture', 'clients'));
    }

    public function update(Request $request, FactureVente $facture)
    {
        // Une facture déjà payée est verrouillée : on ne peut plus la
        // repasser en "en_attente" via ce formulaire générique. Sans ce
        // garde-fou, montant_paye/montant_restant restaient à jour "payé"
        // alors que statut redevenait "en_attente" — ce qui provoquait une
        // collision de numéro de quittance au prochain passage par
        // enregistrerPaiement() (voir correctif précédent).
        if ($facture->statut === 'payee') {
            return back()
                ->withInput()
                ->withErrors([
                    'statut' => "Cette facture est déjà payée et ne peut plus être modifiée depuis ce formulaire.",
                ]);
        }

        // client_id et dateEmissionFacture ne sont plus modifiables : on les fige sur les valeurs existantes
        $data = $request->validate([
            'date_echeance'             => 'required|date|after_or_equal:' . $facture->dateEmissionFacture->format('Y-m-d'),
            // IMPORTANT : "en_retard" et "archive" ne sont JAMAIS choisis manuellement.
            // "en_retard" est piloté uniquement par la commande planifiée factures:maj-statuts.
            // "archive" est piloté uniquement par les routes destroy()/desarchiver().
            'statut'                    => 'required|in:en_attente,payee',
            'pdf_facture'               => 'nullable|file|mimes:pdf|max:10240',
            'lignes'                    => 'required|array|min:1',
            'lignes.*.reference'        => 'nullable|string|max:100',
            'lignes.*.designation'      => 'required|string|max:255',
            'lignes.*.description'      => 'nullable|string|max:255',
            'lignes.*.quantite'         => 'required|numeric|min:0.01',
            'lignes.*.unite'            => 'nullable|string|max:50',
            'lignes.*.prix_unitaire'    => 'required|numeric|min:0',
            'lignes.*.taux_tva'         => 'required|numeric|min:0',
        ], $this->messagesFacture());

        DB::transaction(function () use ($data, $request, $facture) {
            $montantHt  = 0;
            $montantTva = 0;
            foreach ($data['lignes'] as $ligne) {
                $montantLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $montantHt  += $montantLigne;
                $montantTva += $montantLigne * ($ligne['taux_tva'] / 100);
            }
            $nouveauTtc = $montantHt + $montantTva;

            $facture->update([
                // client_id et dateEmissionFacture volontairement absents : non modifiables
                'date_echeance'   => $data['date_echeance'],
                'montant_ht'      => $montantHt,
                'montant_tva'     => $montantTva,
                'montant_ttc'     => $nouveauTtc,
                'montant_restant' => max($nouveauTtc - $facture->montant_paye, 0),
                'statut'          => $data['statut'],
            ]);

            if ($request->hasFile('pdf_facture')) {
                if ($facture->chemin_pdf) {
                    Storage::disk('public')->delete($facture->chemin_pdf);
                }

                $fichier = $request->file('pdf_facture');
                $nomOriginal = $fichier->getClientOriginalName();

                // Dossier dédié à cette facture : factures/ventes/{facture_id}/facture.pdf
                $chemin = $fichier->storeAs(
                    'factures/ventes/' . $facture->id,
                    'facture.' . $fichier->getClientOriginalExtension(),
                    'public'
                );

                $facture->update([
                    'chemin_pdf' => $chemin,
                    'nom_pdf'    => $nomOriginal,
                ]);
            }

            $facture->details()->delete();
            foreach ($data['lignes'] as $ligne) {
                $montantLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $facture->details()->create([
                    'reference'     => $ligne['reference'] ?? null,
                    'designation'   => $ligne['designation'],
                    'description'   => $ligne['description'] ?? '',
                    'quantite'      => $ligne['quantite'],
                    'unite'         => $ligne['unite'] ?? null,
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'taux_tva'      => $ligne['taux_tva'],
                    'montant_ligne' => $montantLigne,
                ]);
            }
        });

        return redirect()->route('factures.ventes.info', $facture->id)->with('success', 'Facture modifiée avec succès.');
    }

    public function destroy(FactureVente $facture)
    {
        $facture->update([
            'statut_avant_archivage' => $facture->statut,
            'statut'                 => 'archive',
        ]);

        return redirect()->route('factures.ventes.index')->with('success', 'Facture archivée.');
    }

    public function desarchiver(FactureVente $facture)
    {
        $facture->update([
            'statut'                 => $facture->statut_avant_archivage ?? 'en_attente',
            'statut_avant_archivage' => null,
        ]);

        return redirect()->route('factures.ventes.archives')->with('success', 'Facture restaurée.');
    }

    /** Affiche la page de paiement (montant TTC, statut, etc.) */
    public function paiement(FactureVente $facture)
    {
        return view('factures.ventes.paiement', compact('facture'));
    }

    /** Affiche/imprime la quittance d'un paiement précis */
    public function quittance(PaiementFactureVente $paiement)
    {
        $facture = $paiement->facture;

        return view('factures.ventes.quittance', compact('paiement', 'facture'));
    }

    /** Enregistre le paiement total (unique) d'une facture et redirige vers la quittance */
    public function enregistrerPaiement(Request $request, FactureVente $facture)
    {
        // Déjà payée : on ne peut pas payer deux fois.
        // On vérifie montant_restant EN PLUS de statut, car statut peut être
        // remis à "en_attente" manuellement via update() (formulaire
        // d'édition) sans que montant_paye/montant_restant ne soient
        // réinitialisés — dans ce cas statut seul ne suffit pas à détecter
        // qu'un paiement existe déjà pour cette facture.
        if ($facture->statut === 'payee' || (float) $facture->montant_restant <= 0) {
            return redirect()->route('factures.ventes.paiement', $facture->id);
        }

        $data = $request->validate([
            'methode_paiement' => ['required', 'in:especes,cheque,virement'],
            'date_paiement'    => ['required', 'date'],
        ]);

        $paiement = DB::transaction(function () use ($data, $facture) {
            // Numéro de quittance séquentiel, basé sur le nombre de
            // paiements déjà enregistrés pour CETTE facture — évite la
            // collision sur la contrainte unique numero_quittance si un
            // deuxième paiement se retrouve associé à la même facture.
            $sequence = $facture->paiements()->count() + 1;
            $numero   = 'QT-' . $facture->numFacture . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            $paiement = $facture->paiements()->create([
                'montant'          => $facture->montant_ttc,
                'methode_paiement' => $data['methode_paiement'],
                'date_paiement'    => $data['date_paiement'],
                'numero_quittance' => $numero,
            ]);

            $facture->update([
                'montant_paye'    => $facture->montant_ttc,
                'montant_restant' => 0,
                'statut'          => 'payee',
            ]);

            return $paiement;
        });

        return redirect()->route('factures.ventes.quittance', $paiement->id)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    /** Génère et télécharge la quittance en PDF */
    public function quittancePdf(PaiementFactureVente $paiement)
    {
        $facture = $paiement->facture;

        $pdf = Pdf::loadView('factures.ventes.quittance-pdf', compact('paiement', 'facture'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quittance-' . $paiement->numero_quittance . '.pdf');
    }

    /** Génère et télécharge la facture en PDF (document propre, pas le fichier uploadé) */
    public function facturePdf(FactureVente $facture)
    {
        $pdf = Pdf::loadView('factures.ventes.facture-pdf', compact('facture'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('facture-' . $facture->numFacture . '.pdf');
    }
}