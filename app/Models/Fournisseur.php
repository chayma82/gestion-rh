<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class Fournisseur extends Model
{
    use  Loggable;
    protected $table = 'fournisseurs';

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
        return $this->hasMany(FactureAchat::class, 'fournisseur_id');
    }
}
