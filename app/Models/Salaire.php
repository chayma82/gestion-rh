<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salaire extends Model
{
    use HasFactory;

    protected $table = 'salaire';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'employe_id',
        'contrat_id',
        'periode',
        'salaire_brut',
        'total_primes',
        'total_avances',
        'statut',
    ];

    protected $casts = [
        'salaire_brut'       => 'decimal:2',
        'total_primes'       => 'decimal:2',
        'total_avances'      => 'decimal:2',
        'date_creation'      => 'datetime',
        'date_modification'  => 'datetime',
        'date_paiement'      => 'datetime',
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

    public function getSalaireNetAttribute()
    {
        return (
            (float) $this->salaire_brut
            + (float) $this->total_primes
            - (float) $this->total_avances
        );
    }

    /**
     * Liste des statuts possibles, utilisée pour le filtre
     * dans la vue liste.blade.php.
     */
    public static function statuts(): array
    {
        return [
            'en_attente' => 'En attente',
            'paye'       => 'Payé',
            'annule'     => 'Annulé',
        ];
    }

    /**
     * Badge (libellé + classes CSS) affiché dans le tableau.
     */
    public function getStatutBadgeAttribute(): array
    {
        return match ($this->statut) {
            'paye' => [
                'label'   => 'Payé',
                'classes' => 'bg-green-50 text-green-700',
            ],
            'annule' => [
                'label'   => 'Annulé',
                'classes' => 'bg-gray-100 text-gray-500',
            ],
            default => [
                'label'   => 'En attente',
                'classes' => 'bg-orange-50 text-orange-700',
            ],
        };
    }
}
