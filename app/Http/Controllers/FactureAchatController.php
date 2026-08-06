<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\FactureAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FactureAchatController extends Controller
{
    public function index(Request $request)
    {
        $entrepriseId =  1;

        $factures = FactureAchat::actives()
            ->where('entreprise_id', $entrepriseId)
            ->when($request->q, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('numFacture', 'like', '%' . $request->q . '%')
                    ->orWhereHas('fournisseur', function ($query) use ($request) {
                    $query->where('nom', 'like', '%' . $request->q . '%');
            });
                    });
            })
            ->when($request->mois, function ($query) use ($request) {
                $query->whereMonth('dateEmissionFacture', $request->mois);
            })
            ->latest('dateEmissionFacture')
            ->paginate(15)
            ->withQueryString();

        $base = FactureAchat::actives()->where('entreprise_id', $entrepriseId);

        $totalFactures     = (clone $base)->count();
        $facturesPayees    = (clone $base)->where('statut', 'payee')->count();
        $facturesEnRetard  = (clone $base)->where('statut', 'en_retard')->count();
        $montantTotalTtc   = (clone $base)->sum('montant_ttc');

        return view('factures.achats.liste', compact(
            'factures', 'totalFactures', 'facturesPayees', 'facturesEnRetard', 'montantTotalTtc'
        ));
    }

    public function archives()
    {
        $factures = FactureAchat::archivees()
            ->where('entreprise_id',  1)
            ->latest('updated_at')
            ->paginate(15);

        return view('factures.achats.archives', compact('factures'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('entreprise_id',  1)->orderBy('nom')->get();

        return view('factures.achats.create', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fournisseur_id'                  => 'required|exists:fournisseurs,id',
            'dateEmissionFacture'              => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:dateEmissionFacture',            'taux_tva'                         => 'required|numeric|min:0',
            'pdf_facture'                      => 'nullable|file|mimes:pdf|max:10240',
            'lignes'                           => 'required|array|min:1',
            'lignes.*.reference_produit'       => 'nullable|string|max:50',
            'lignes.*.description'             => 'required|string|max:255',
            'lignes.*.quantite'                => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire'           => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            $fournisseur = Fournisseur::findOrFail($data['fournisseur_id']);
            $entrepriseId =  1;

            $montantHt = 0;
            foreach ($data['lignes'] as $ligne) {
                $montantHt += $ligne['quantite'] * $ligne['prix_unitaire'];
            }
            $montantTva = $montantHt * ($data['taux_tva'] / 100);

            $dernierNumero = FactureAchat::where('entreprise_id', $entrepriseId)->count() + 1;

            $facture = FactureAchat::create([
                'tenant_id'           =>  1,
                'entreprise_id'       => $entrepriseId,
                'fournisseur_id'      => $fournisseur->id,
                'numFacture'          => 'FA-' . now()->year . '-' . str_pad($dernierNumero, 4, '0', STR_PAD_LEFT),
                'dateEmissionFacture' => $data['dateEmissionFacture'],
                'date_echeance'       => $data['date_echeance'] ?? null,
                'montant_ht'          => $montantHt,
                'montant_tva'         => $montantTva,
                'montant_ttc'         => $montantHt + $montantTva,
                'montant_paye'        => 0,
                'statut'              => 'en_attente',
            ]);

            if ($request->hasFile('pdf_facture')) {
                $fichier = $request->file('pdf_facture');
                $nomOriginal = $fichier->getClientOriginalName();

                // Dossier dédié à cette facture : factures/achats/{facture_id}/facture.pdf
                $chemin = $fichier->storeAs(
                    'factures/achats/' . $facture->id,
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
                    'reference_produit' => $ligne['reference_produit'] ?? null,
                    'description'       => $ligne['description'],
                    'quantite'          => $ligne['quantite'],
                    'prix_unitaire'     => $ligne['prix_unitaire'],
                    'montant_ligne'     => $montantLigne,
                ]);
            }
        });

        return redirect()->route('factures.achats.index')->with('success', 'Facture d\'achat créée avec succès.');
    }

    public function info(FactureAchat $facture)
    {
        return view('factures.achats.info', compact('facture'));
    }

    public function edit(FactureAchat $facture)
    {
        $fournisseurs = Fournisseur::where('entreprise_id',  1)->orderBy('nom')->get();

        return view('factures.achats.edit', compact('facture', 'fournisseurs'));
    }

    public function update(Request $request, FactureAchat $facture)
    {
        $data = $request->validate([
            'fournisseur_id'                  => 'required|exists:fournisseurs,id',
            'dateEmissionFacture'              => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:dateEmissionFacture',
            'taux_tva'                         => 'required|numeric|min:0',
            // IMPORTANT : "en_retard" et "archive" ne sont JAMAIS choisis manuellement.
            // "en_retard" est piloté uniquement par la commande planifiée factures:maj-statuts.
            // "archive" est piloté uniquement par les routes destroy()/desarchiver().
            'statut'                           => 'required|in:en_attente,payee',
            'pdf_facture'                      => 'nullable|file|mimes:pdf|max:10240',
            'lignes'                           => 'required|array|min:1',
            'lignes.*.reference_produit'       => 'nullable|string|max:50',
            'lignes.*.description'             => 'required|string|max:255',
            'lignes.*.quantite'                => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire'           => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $request, $facture) {
            $fournisseur = Fournisseur::findOrFail($data['fournisseur_id']);

            $montantHt = 0;
            foreach ($data['lignes'] as $ligne) {
                $montantHt += $ligne['quantite'] * $ligne['prix_unitaire'];
            }
            $montantTva = $montantHt * ($data['taux_tva'] / 100);

            $facture->update([
                'fournisseur_id'      => $fournisseur->id,
                'dateEmissionFacture' => $data['dateEmissionFacture'],
                'date_echeance'       => $data['date_echeance'] ?? null,
                'montant_ht'          => $montantHt,
                'montant_tva'         => $montantTva,
                'montant_ttc'         => $montantHt + $montantTva,
                'statut'              => $data['statut'],
            ]);

            if ($request->hasFile('pdf_facture')) {
                if ($facture->chemin_pdf) {
                    Storage::disk('public')->delete($facture->chemin_pdf);
                }

                $fichier = $request->file('pdf_facture');
                $nomOriginal = $fichier->getClientOriginalName();

                // Dossier dédié à cette facture : factures/achats/{facture_id}/facture.pdf
                $chemin = $fichier->storeAs(
                    'factures/achats/' . $facture->id,
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
                    'reference_produit' => $ligne['reference_produit'] ?? null,
                    'description'       => $ligne['description'],
                    'quantite'          => $ligne['quantite'],
                    'prix_unitaire'     => $ligne['prix_unitaire'],
                    'montant_ligne'     => $montantLigne,
                ]);
            }
        });

        return redirect()->route('factures.achats.info', $facture->id)->with('success', 'Facture modifiée avec succès.');
    }

    public function destroy(FactureAchat $facture)
    {
        $facture->update([
            'statut_avant_archivage' => $facture->statut,
            'statut'                 => 'archive',
        ]);

        return redirect()->route('factures.achats.index')->with('success', 'Facture archivée.');
    }

    public function desarchiver(FactureAchat $facture)
    {
        $facture->update([
            'statut'                 => $facture->statut_avant_archivage ?? 'en_attente',
            'statut_avant_archivage' => null,
        ]);

        return redirect()->route('factures.achats.archives')->with('success', 'Facture restaurée.');
    }

    public function marquerPayee(FactureAchat $facture)
    {
        $facture->update(['statut' => 'payee', 'montant_paye' => $facture->montant_ttc]);

        return back()->with('success', 'Facture marquée comme payée.');
    }

    public function annulerPaiement(FactureAchat $facture)
    {
        // Ne pas forcer "en_attente" à l'aveugle : si l'échéance est déjà
        // dépassée, la facture doit repasser en "en_retard", pas en "en_attente".
        $nouveauStatut = ($facture->date_echeance && $facture->date_echeance->lt(now()->startOfDay()))
            ? 'en_retard'
            : 'en_attente';

        $facture->update(['statut' => $nouveauStatut, 'montant_paye' => 0]);

        return back()->with('success', 'Paiement annulé.');
    }

    public function payerTout()
    {
        FactureAchat::actives()
            ->where('entreprise_id',  1)
            ->where('statut', '!=', 'payee')
            ->get()
            ->each(fn ($f) => $f->update(['statut' => 'payee', 'montant_paye' => $f->montant_ttc]));

        return back()->with('success', 'Toutes les factures ont été marquées comme payées.');
    }
}
