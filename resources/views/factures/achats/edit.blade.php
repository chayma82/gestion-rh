@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier la facture {{ $facture->numFacture }}</h1>
        <a href="{{ route('factures.achats.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
            <i class="fa-solid fa-arrow-left"></i> Annuler
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('factures.achats.update', $facture->id) }}" method="POST" id="formFacture" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informations générales</h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fournisseur</label>
                    <select name="fournisseur_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        @foreach($fournisseurs as $fournisseur)
                            <option value="{{ $fournisseur->id }}" @selected(old('fournisseur_id', $facture->fournisseur_id) == $fournisseur->id)>{{ $fournisseur->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date d'émission</label>
                    <input type="date" name="dateEmissionFacture" required
                           value="{{ old('dateEmissionFacture', $facture->dateEmissionFacture->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date échéance</label>
                    <input type="date" name="date_echeance"
                           value="{{ old('date_echeance', $facture->date_echeance?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Taux de TVA (%)</label>
                    @php
                        $tauxActuel = $facture->montant_ht > 0 ? round(($facture->montant_tva / $facture->montant_ht) * 100, 2) : 19;
                    @endphp
                    <input type="number" step="0.01" name="taux_tva" id="tauxTva" required
                           value="{{ old('taux_tva', $tauxActuel) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
                    {{--
                        IMPORTANT : "En retard" n'est plus une option choisissable ici.
                        Ce statut est calculé et écrit automatiquement par la commande planifiée
                        "factures:maj-statuts" (voir app/Console/Commands/MettreAJourStatutFactures.php),
                        en comparant la date d'échéance à la date du jour, chaque minute.
                        Le laisser modifiable à la main créait des incohérences (facture marquée
                        "En retard" alors que l'échéance n'était pas dépassée, ou l'inverse).
                    --}}
                    <select name="statut"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        <option value="en_attente" @selected(old('statut', $facture->statut) == 'en_attente')>En attente</option>
                        <option value="payee" @selected(old('statut', $facture->statut) == 'payee')>Payée</option>
                    </select>
                    @if($facture->statut === 'en_retard')
                        <p class="mt-1 text-xs text-orange-500">
                            <i class="fa-solid fa-clock"></i>
                            Statut actuel : <strong>En retard</strong> (calculé automatiquement selon l'échéance — enregistrer ce formulaire la repassera en "En attente").
                        </p>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fichier PDF de la facture</label>
                    <input type="file" name="pdf_facture" accept="application/pdf"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-orange-50 file:text-[#E2721B] hover:file:bg-orange-100">
                    @if($facture->chemin_pdf)
                        <p class="mt-1 text-xs text-gray-500">
                            Fichier actuel :
                            <a href="{{ asset('storage/' . $facture->chemin_pdf) }}" target="_blank" class="text-[#E2721B] hover:underline">{{ $facture->nom_pdf ?? 'voir le PDF' }}</a>
                            — envoyer un nouveau fichier le remplacera.
                        </p>
                    @else
                        <p class="mt-1 text-xs text-gray-400">Aucun fichier joint (format .pdf, 10 Mo max).</p>
                    @endif
                </div>

            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Produits achetés</h2>
            </div>

            @php
                $anciennesLignes = old('lignes');
                if (! $anciennesLignes) {
                    $anciennesLignes = $facture->details->map(fn($d) => [
                        'reference_produit' => $d->reference_produit,
                        'description'       => $d->description,
                        'quantite'          => $d->quantite,
                        'prix_unitaire'     => $d->prix_unitaire,
                    ])->toArray();
                }
                if (empty($anciennesLignes)) {
                    $anciennesLignes = [['reference_produit' => '', 'description' => '', 'quantite' => 1, 'prix_unitaire' => '']];
                }
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]" id="tableLignes">
                    <thead class="bg-orange-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Référence</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Désignation du produit</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantité</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix unitaire</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant ligne</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="corpsLignes" class="divide-y divide-gray-100">
                        @foreach($anciennesLignes as $i => $ligne)
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="text" name="lignes[{{ $i }}][reference_produit]"
                                           value="{{ $ligne['reference_produit'] ?? '' }}"
                                           class="w-24 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="lignes[{{ $i }}][description]" required
                                           value="{{ $ligne['description'] ?? '' }}"
                                           class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" name="lignes[{{ $i }}][quantite]"
                                           value="{{ $ligne['quantite'] ?? 1 }}" required
                                           class="ligne-qte w-20 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" name="lignes[{{ $i }}][prix_unitaire]"
                                           value="{{ $ligne['prix_unitaire'] ?? '' }}" required
                                           class="ligne-prix w-28 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-3 ligne-montant text-sm font-medium text-gray-700">0.00 DT</td>
                                <td class="px-4 py-3">
                                    <button type="button" class="btn-supprimer-ligne px-3 py-1.5 rounded-lg bg-white border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 flex items-center justify-between">
                <button type="button" id="btnAjouterLigne"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-sm font-medium text-gray-700 transition">
                    <i class="fa-solid fa-plus"></i> Ajouter un produit
                </button>

                <div class="text-right text-sm text-gray-600 space-y-1">
                    <p>Montant Hors Taxe (HT) : <span id="totalHt" class="font-semibold text-gray-900">0.00 DT</span></p>
                    <p>Montant TVA : <span id="totalTva" class="font-semibold text-gray-900">0.00 DT</span></p>
                    <p class="text-base">Montant TTC : <span id="totalTtc" class="font-bold text-[#E2721B]">0.00 DT</span></p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-check"></i> Enregistrer les modifications
            </button>
        </div>

    </form>

</div>

<script>
    let indexLigne = document.querySelectorAll('#corpsLignes tr').length;

    function recalculerTotaux() {
        let totalHt = 0;
        document.querySelectorAll('#corpsLignes tr').forEach(function (ligne) {
            const qte = parseFloat(ligne.querySelector('.ligne-qte')?.value) || 0;
            const prix = parseFloat(ligne.querySelector('.ligne-prix')?.value) || 0;
            const montant = qte * prix;
            const cellule = ligne.querySelector('.ligne-montant');
            if (cellule) cellule.textContent = montant.toFixed(2) + ' DT';
            totalHt += montant;
        });

        const tauxTva = parseFloat(document.getElementById('tauxTva').value) || 0;
        const totalTva = totalHt * (tauxTva / 100);
        const totalTtc = totalHt + totalTva;

        document.getElementById('totalHt').textContent = totalHt.toFixed(2) + ' DT';
        document.getElementById('totalTva').textContent = totalTva.toFixed(2) + ' DT';
        document.getElementById('totalTtc').textContent = totalTtc.toFixed(2) + ' DT';
    }

    document.getElementById('btnAjouterLigne').addEventListener('click', function () {
        const corps = document.getElementById('corpsLignes');
        const ligne = corps.querySelector('tr').cloneNode(true);
        ligne.querySelectorAll('input').forEach(function (champ) {
            champ.name = champ.name.replace(/\[\d+\]/, '[' + indexLigne + ']');
            champ.value = champ.name.includes('quantite') ? '1' : '';
        });
        ligne.querySelector('.ligne-montant').textContent = '0.00 DT';
        corps.appendChild(ligne);
        indexLigne++;
    });

    document.getElementById('corpsLignes').addEventListener('click', function (e) {
        const bouton = e.target.closest('.btn-supprimer-ligne');
        if (bouton) {
            const lignes = document.querySelectorAll('#corpsLignes tr');
            if (lignes.length > 1) {
                bouton.closest('tr').remove();
                recalculerTotaux();
            }
        }
    });

    document.getElementById('formFacture').addEventListener('input', function (e) {
        if (e.target.matches('.ligne-qte, .ligne-prix, #tauxTva')) recalculerTotaux();
    });

    recalculerTotaux();
</script>

@endsection
