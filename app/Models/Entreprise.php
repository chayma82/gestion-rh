<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class Entreprise extends Model
{
    use HasFactory, Loggable;
    protected $table = 'entreprise';

    public $timestamps = false;

    // Valeurs possibles pour la colonne "type_entreprise" (enum SQL)
    const TYPE_RH = 'rh';
    const TYPE_AUTRE = 'autre';

    const TYPES_ENTREPRISE = [
        self::TYPE_RH    => 'RH (Ressources Humaines / Recrutement)',
        self::TYPE_AUTRE => 'Autre',
    ];

    protected $fillable = [
        'tenant_id',
        'nom',
        'email',
        'type_entreprise',
        'secteur_activite',
        'adresse',
        'ville',
        'code_postal',
        'num_fiscal',
        'telephone',
    ];

    public function getTypeEntrepriseLabelAttribute(): string
    {
        return self::TYPES_ENTREPRISE[$this->type_entreprise] ?? $this->type_entreprise;
    }

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

    public function factureschat()
    {
        return $this->hasMany(FactureAchat::class, 'entreprise_id');
    }
    public function facturesvente()
    {
        return $this->hasMany(FactureVente::class, 'entreprise_id');
    }

}
