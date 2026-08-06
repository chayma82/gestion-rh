<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste des clients actifs
     */
    public function index(Request $request)
    {
        $clients = Client::actives()
            ->where('entreprise_id', 1)
            ->when($request->q, function ($query) use ($request) {
                $query->where('nom', 'like', '%' . $request->q . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * Archiver un client
     */
    public function archiver(Request $request, Client $client)
    {
        abort_unless($client->entreprise_id === 1, 403);

        $client->update([
            'status' => 'archive'
        ]);

        return back()->with('success', 'Client archivé avec succès.');
    }

    /**
     * Liste des clients archivés
     */
    public function archives(Request $request)
    {
        $clients = Client::archivees()
            ->where('entreprise_id', 1)
            ->when($request->q, function ($query) use ($request) {
                $query->where('nom', 'like', '%' . $request->q . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.archives', compact('clients'));
    }

    /**
     * Restaurer un client
     */
    public function desarchiver(Client $client)
    {
        abort_unless($client->entreprise_id === 1, 403);

        $client->update([
            'status' => 'actif'
        ]);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client restauré avec succès.');
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un client
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'              => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'telephone'        => 'nullable|string|max:30',
            'adresse'          => 'nullable|string|max:255',
            'matricule_fiscal' => 'required|string|max:50',
        ]);

        $data['tenant_id']     = 1;
        $data['entreprise_id'] = 1;
        $data['status']        = 'actif';

        Client::create($data);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client ajouté avec succès.');
    }

    /**
     * Formulaire de modification
     */
    public function edit(Client $client)
    {
        abort_unless($client->entreprise_id === 1, 403);

        return view('clients.edit', compact('client'));
    }

    /**
     * Modifier un client
     */
    public function update(Request $request, Client $client)
    {
        abort_unless($client->entreprise_id === 1, 403);

        $data = $request->validate([
            'nom'              => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'telephone'        => 'nullable|string|max:30',
            'adresse'          => 'nullable|string|max:255',
            'matricule_fiscal' => 'required|string|max:50',
        ]);

        $client->update($data);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client modifié avec succès.');
    }

    /**
     * Archiver un client (alias conservé pour compat. route resource "destroy")
     */
    public function destroy(Client $client)
    {
        return $this->archiver(request(), $client);
    }
}
