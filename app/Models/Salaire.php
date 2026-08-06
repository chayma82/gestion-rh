<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    use HasFactory;

    protected $table = 'salaire';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id', 'employe_id', 'contrat_id', 'periode',
        'salaire_brut', 'total_primes', 'total_avances',
        'statut', 'date_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'salaire_brut' => 'decimal:2',
        'total_primes' => 'decimal:2',
        'total_avances' => 'decimal:2',
        'date_creation' => 'datetime',
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

    public function getNetAttribute()
    {
        return $this->salaire_brut + $this->total_primes - $this->total_avances;
    }

    public function estPaye(): bool
    {
        return $this->statut === 'paye';
    }
}
