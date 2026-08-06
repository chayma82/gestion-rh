<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $table = 'entreprise';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'nom',
        'email',
        'secteur_activite',
        'adresse',
        'ville',
        'code_postal',
        'num_fiscal',
        'telephone',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'entreprise_id');
    }

    public function employes()
    {
        return $this->hasMany(Employe::class, 'entreprise_id');
    }

    public function factures()
    {
        return $this->hasMany(Facture::class, 'entreprise_id');
    }
}
