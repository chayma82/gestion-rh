<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Loggable;

class Prime extends Model
{
    use HasFactory, Loggable;
    protected $table = 'primes';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id',
        'employe_id',
        'contrat_id',
        'montant',
        'date_prime',
        'motif',
    ];

    protected $casts = [
        'montant'     => 'decimal:2',
        'date_prime' => 'date',
        'date_creation' => 'datetime',
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
}
