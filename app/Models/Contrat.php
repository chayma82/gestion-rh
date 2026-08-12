<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrat extends Model
{
    use HasFactory;

    protected $table = 'contrat';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'employe_id',
        'departement_id',
        'poste_id',
        'numcontrat',
        'typeContrat',
        'date_debut',
        'date_fin',
        'statut',
        'salaire_base',
        'nbreJourCongeAqcuise',
        'recreteur',
    ];

    protected $casts = [
        'date_debut'           => 'date',
        'date_fin'             => 'date',
        'salaire_base'         => 'decimal:2',
        'nbreJourCongeAqcuise' => 'integer',
        'date_creation'       => 'datetime',
        'date_modification'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    public function salaires()
    {
        return $this->hasMany(Salaire::class, 'contrat_id');
    }

    public function conges()
    {
        return $this->hasMany(Conge::class, 'contrat_id');
    }

    public function primes()
    {
        return $this->hasMany(Prime::class, 'contrat_id');
    }

    public function avancesalaires()
    {
        return $this->hasMany(AvanceSalaire::class, 'contrat_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getSalaireActuelAttribute()
    {
        return $this->salaires()
            ->latest('periode')
            ->first();
    }

    public function getSoldeCongeAttribute()
    {
        $joursUtilises = $this->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {
                return $conge->date_debut->diffInDays($conge->date_fin) + 1;
            });

        return max(
            0,
            $this->nbreJourCongeAqcuise - $joursUtilises
        );
    }

    public function getCongesCumulesAttribute()
    {
        return $this->conges()
            ->where('type_conge', 'paye')
            ->get()
            ->sum(function ($conge) {
                return $conge->date_debut->diffInDays($conge->date_fin) + 1;
            });
    }

    public function duree()
    {
        if (!$this->date_debut || !$this->date_fin) {
            return null;
        }

        return $this->date_debut->diffInMonths($this->date_fin);
    }

    /*
    |--------------------------------------------------------------------------
    | Statuts contrat
    |--------------------------------------------------------------------------
    */

    public static function statuts(): array
    {
        return [
            'a_venir' => 'À venir',
            'actif'   => 'Actif',
            'expire'  => 'Expiré',
            'resilie' => 'Résilié',
        ];
    }

    public function getStatutBadgeAttribute(): array
    {
        $map = [
            'a_venir' => [
                'label' => 'À venir',
                'classes' => 'bg-blue-50 text-blue-600'
            ],

            'actif' => [
                'label' => 'Actif',
                'classes' => 'bg-green-50 text-green-700'
            ],

            'expire' => [
                'label' => 'Expiré',
                'classes' => 'bg-gray-100 text-gray-600'
            ],

            'resilie' => [
                'label' => 'Résilié',
                'classes' => 'bg-red-50 text-red-600'
            ],
        ];

        return $map[$this->statut] ?? [
            'label' => $this->statut,
            'classes' => 'bg-gray-100 text-gray-600'
        ];
    }
}
