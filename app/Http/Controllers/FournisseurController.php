<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $fournisseurs = Fournisseur::actives()
            ->where('entreprise_id', current_entreprise_id())
            ->when($request->q, fn($q) => $q->where('nom', 'like', '%'.$request->q.'%'))
            ->paginate(15)
            ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function archiver(Fournisseur $fournisseur)
    {
        abort_unless($fournisseur->entreprise_id === current_entreprise_id(), 403);

        $fournisseur->update(['status' => 'archive']);

        return back()->with('success', 'Fournisseur archivé avec succès.');
    }

    public function archives(Request $request)
    {
        $fournisseurs = Fournisseur::archivees()
            ->where('entreprise_id', current_entreprise_id())
            ->when($request->q, fn($q) => $q->where('nom', 'like', '%'.$request->q.'%'))
            ->paginate(15)
            ->withQueryString();

        return view('fournisseurs.archives', compact('fournisseurs'));
    }

    public function desarchiver(Fournisseur $fournisseur)
    {
        abort_unless($fournisseur->entreprise_id === current_entreprise_id(), 403);

        $fournisseur->update(['status' => 'actif']);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur restauré avec succès.');
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'              => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'telephone'        => 'nullable|string|max:30',
            'adresse'          => 'nullable|string|max:255',
            'matricule_fiscal' => 'required|nullable|string|max:50',
        ]);

        $data['tenant_id']     = current_tenant_id();
        $data['entreprise_id'] = current_entreprise_id();
        $data['status']        = 'actif';

        Fournisseur::create($data);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(Fournisseur $fournisseur)
    {
        abort_unless($fournisseur->entreprise_id === current_entreprise_id(), 403);

        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        abort_unless($fournisseur->entreprise_id === current_entreprise_id(), 403);

        $data = $request->validate([
            'nom'              => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'telephone'        => 'nullable|string|max:30',
            'adresse'          => 'nullable|string|max:255',
            'matricule_fiscal' => 'required|nullable|string|max:50',
        ]);

        $fournisseur->update($data);

        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur modifié avec succès.');
    }

    /**
     * Archiver un fournisseur (alias conservé pour compat. route resource "destroy")
     * On n'effectue plus de suppression définitive : on archive, comme pour les factures.
     */
    public function destroy(Fournisseur $fournisseur)
    {
        return $this->archiver($fournisseur);
    }
}