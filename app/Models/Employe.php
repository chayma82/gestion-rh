<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employe extends Model
{
    use HasFactory;

    protected $table = 'employe';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';
protected $appends = [
    'solde_conge',
    'nom_complet',
    'matricule_nom_complet'
];
    protected $fillable = [
        'tenant_id',
        'entreprise_id',
        'utilisateur_creation_id',

        'departement_id',
        'poste_id',
        'matricule',
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
        'date_embauche',
        'statutEmploye',
        'nbreJourCongeAqcuise',
        'nom_contact_urgence',
        'lien_parente',
        'telephone_urgence',
        'adresse_urgence',
    ];

    protected $casts = [
        'date_naissance'      => 'date',
        'date_embauche'       => 'date',
        'nb_enfants'          => 'integer',
        'nbreJourCongeAqcuise' => 'integer',
        'date_creation'       => 'datetime',
        'date_modification'   => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function utilisateurCreation()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_creation_id');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'employe_id');
    }

    public function conges()
    {
        return $this->hasMany(Conge::class, 'employe_id');
    }

    public function salaires()
    {
        return $this->hasMany(Salaire::class, 'employe_id');
    }

    public function avancesalaires()
    {
        return $this->hasMany(Avancesalaire::class, 'employe_id');
    }

    public function primes()
    {
        return $this->hasMany(Prime::class, 'employe_id');
    }

    public function contratActif()
    {
        return $this->hasOne(Contrat::class, 'employe_id')
                    ->where('statut', 'actif');
    }

    public function contratEnCours()
    {
        return $this->hasOne(Contrat::class, 'employe_id')
                    ->whereIn('statut', ['actif', 'a_venir'])
                    ->latest('date_debut');
    }
    public function getSalaireNetAttribute()
    {
        $salaire = $this->salaires()->latest('date_creation')->first();

        if (!$salaire) {
            return 0;
        }

        return $salaire->salaire_brut;
    }

    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function getMatriculeNomCompletAttribute()
    {
        return $this->matricule . ' ' . $this->nom . ' ' . $this->prenom;
    }

    /**
     * Solde de congés payés restant = jours acquis - jours déjà pris (type paye uniquement).
     */
    public function getSoldeCongeAttribute()
    {
        $joursUtilises = $this->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {

                return $conge->date_debut
                    ->diffInDays($conge->date_fin) + 1;

            });

        return $this->nbreJourCongeAqcuise - $joursUtilises;
    }

    /**
     * Nombre total de jours de congés payés déjà pris (utile pour l'affichage
     * "Congés Cumulés" dans la fiche employé). Ajouté car la vue l'utilisait
     * sans que l'accessor n'existe.
     */
    public function getCongesCumulesAttribute()
    {
        return $this->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {

                return $conge->date_debut
                    ->diffInDays($conge->date_fin) + 1;

            });
    }
}
