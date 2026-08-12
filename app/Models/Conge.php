<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conge extends Model
{
    use HasFactory;

    protected $table = 'conge';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'employe_id',
        'contrat_id',
        'type_conge',
        'date_debut',
        'date_fin',
        'motif',
        'justificatif',
    ];

    protected $casts = [
        'date_debut'        => 'date',
        'date_fin'          => 'date',
        'date_creation'     => 'datetime',
        'date_modification' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'contrat_id');
    }

    public function getNombreJoursAttribute()
    {
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Statuts congé (calculés à partir des dates, pas de colonne "statut")
    |--------------------------------------------------------------------------
    */

    public static function statuts(): array
    {
        return [
            'a_venir' => 'À venir',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
        ];
    }

    public function getStatutAttribute(): string
    {
        $aujourdhui = \Carbon\Carbon::today();

        if ($this->date_debut->greaterThan($aujourdhui)) {
            return 'a_venir';
        }

        if ($this->date_fin->lessThan($aujourdhui)) {
            return 'termine';
        }

        return 'en_cours';
    }

    public function getStatutBadgeAttribute(): array
    {
        $map = [
            'a_venir' => [
                'label' => 'À venir',
                'classes' => 'bg-blue-50 text-blue-600',
            ],

            'en_cours' => [
                'label' => 'En cours',
                'classes' => 'bg-green-50 text-green-700',
            ],

            'termine' => [
                'label' => 'Terminé',
                'classes' => 'bg-gray-100 text-gray-600',
            ],
        ];

        return $map[$this->statut] ?? [
            'label' => $this->statut,
            'classes' => 'bg-gray-100 text-gray-600',
        ];
    }
}
