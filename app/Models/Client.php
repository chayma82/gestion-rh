<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'nom', 'email', 'telephone', 'matricule_fiscal',
        'adresse', 'status', 'tenant_id', 'entreprise_id',
    ];

    // Scopes alignés sur la méthode d'archivage utilisée par FactureVente / FactureAchat
    public function scopeActives($query)
    {
        return $query->where('status', '!=', 'archive');
    }

    public function scopeArchivees($query)
    {
        return $query->where('status', 'archive');
    }

    public function factures()
    {
        return $this->hasMany(FactureVente::class, 'client_id');
    }
}
