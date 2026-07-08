<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employe extends Model
{
    use HasFactory;

    protected $table = 'employes';

    protected $fillable = [
        'id',
        'num_contrat',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'cin_passeport',
        'situation_familiale',
        'nb_enfants',
        'adresse',
        'ville',
        'code_postal',
        'tel_perso',
        'tel_pro',
        'email_perso',
        'email_pro',
        'matricule',
        'departement',
        'poste_occupe',
        'type_contrat',
        'date_Emboche',
        'date_prisePoste',
        'nom_urgence',
        'lien_parente',
        'tel_urgence',
        'adresse_urgence',
        'numero_contrat',
        'status',
        'date_debut',
        'date_fin',
        'salaire',
        'NomRecruteur',
        'nbrejoursconge',
    ];
 public function fullName(): string
    {
        return "{$this->nom} {$this->prenom}";
    }
}
