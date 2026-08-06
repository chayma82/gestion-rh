<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvanceSalaire extends Model
{
    use HasFactory;

    protected $table = 'avance_salaire';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'employe_id',
        'contrat_id',
        'montant',
        'date_avance',
        'motif',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_avance' => 'date',
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
}
