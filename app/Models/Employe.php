<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Loggable;

class Employe extends Model
{
    use HasFactory, Loggable;
    protected $table = 'employe';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $appends = [
        'nom_complet',
        'matricule_nom_complet',
        'solde_conge',
        'conges_cumules',
        'salaire_net',
    ];

    protected $fillable = [
        'tenant_id',
        'entreprise_id',
        'utilisateur_creation_id',
        'statutEmploye',
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

        'nom_contact_urgence',
        'lien_parente',
        'telephone_urgence',
        'adresse_urgence',
    ];

    protected $casts = [
        'date_naissance'    => 'date',
        'nb_enfants'        => 'integer',
        'date_creation'     => 'datetime',
        'date_modification' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations générales
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Relations RH
    |--------------------------------------------------------------------------
    */

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'employe_id');
    }

    public function contratActif()
    {
        return $this->hasOne(Contrat::class, 'employe_id')
            ->where('statut', 'actif')
            ->latestOfMany('date_debut');
    }

    public function contratEnCours()
    {
        return $this->hasOne(Contrat::class, 'employe_id')
            ->whereIn('statut', ['a_venir', 'actif'])
            ->latestOfMany('date_debut');
    }

    public function salaires()
    {
        return $this->hasMany(Salaire::class, 'employe_id');
    }

    public function conges()
    {
        return $this->hasMany(Conge::class, 'employe_id');
    }

    public function primes()
    {
        return $this->hasMany(Prime::class, 'employe_id');
    }

    public function avancesalaires()
    {
        return $this->hasMany(AvanceSalaire::class, 'employe_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNomCompletAttribute()
    {
        return trim($this->nom . ' ' . $this->prenom);
    }

    public function getMatriculeNomCompletAttribute()
    {
        return trim(
            $this->matricule . ' ' .
            $this->nom . ' ' .
            $this->prenom
        );
    }

    /**
     * Salaire brut du contrat actuellement actif.
     */
    public function getSalaireNetAttribute()
{
    $contrat = $this->contratEnCours; // ⚠️ pas contratActif

    if (!$contrat) {
        return 0;
    }

    $salaire = $contrat->salaires()
        ->latest('periode')
        ->first();

    if (!$salaire) {
        return $contrat->salaire_base ?? 0;
    }

    return $salaire->salaire_brut;
}

    /**
     * Nombre de jours de congés acquis du contrat actif.
     */
    public function getSoldeCongeAttribute()
    {
        $contrat = $this->contratActif;

        if (!$contrat) {
            return 0;
        }

        $joursUtilises = $contrat->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {
                return $conge->date_debut->diffInDays($conge->date_fin) + 1;
            });

        return max(
            0,
            ($contrat->nbreJourCongeAqcuise ?? 0) - $joursUtilises
        );
    }

    public function getCongesCumulesAttribute()
    {
        $contrat = $this->contratActif;

        if (!$contrat) {
            return 0;
        }

        return $contrat->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {
                return $conge->date_debut->diffInDays($conge->date_fin) + 1;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Statut
    |--------------------------------------------------------------------------
    |
    | statutEmploye est une vraie colonne de la table employe (voir $fillable).
    | Ne PAS ajouter d'accessor getStatutEmployeAttribute() ici : cela
    | masquerait la colonne réelle et casserait la lecture/l'écriture du
    | statut (ex: ancien code qui faisait $this->contratActif?->statutEmploye,
    | un attribut qui n'existe même pas sur Contrat -> toujours null).
    |
    */

    public function getStatutBadgeAttribute(): array
    {
        $map = [
            'attente_contrat' => [
                'label' => 'En attente de contrat',
                'classes' => 'bg-blue-50 text-blue-600'
            ],

            'attente_prise_poste' => [
                'label' => 'Attente prise de poste',
                'classes' => 'bg-blue-50 text-blue-600'
            ],

            'actif' => [
                'label' => 'Actif',
                'classes' => 'bg-green-50 text-green-700'
            ],

            'en_conge' => [
                'label' => 'En congé',
                'classes' => 'bg-yellow-50 text-yellow-600'
            ],

            'suspendu' => [
                'label' => 'Suspendu',
                'classes' => 'bg-orange-50 text-orange-600'
            ],

            'fin_contrat' => [
                'label' => 'Fin de contrat',
                'classes' => 'bg-gray-100 text-gray-600'
            ],

            'demissionnaire' => [
                'label' => 'Démissionnaire',
                'classes' => 'bg-red-50 text-red-600'
            ],

            'archive' => [
                'label' => 'Archivé',
                'classes' => 'bg-gray-200 text-gray-500'
            ],
        ];

        $statut = $this->statutEmploye;

        return $map[$statut] ?? [
            'label' => $statut ?? 'Inconnu',
            'classes' => 'bg-gray-100 text-gray-600'
        ];
    }
    public function getContratRecentAttribute()
{
    $priorite = [
        'actif'   => 0,
        'a_venir' => 1,
        'expire'  => 2,
        'resilie' => 3,
    ];

    return $this->contrats
        ->sortByDesc('date_debut')
        ->sortBy(fn ($c) => $priorite[$c->statut] ?? 4)
        ->first();
}
}
