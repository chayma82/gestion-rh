<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;

    protected $table = 'conge';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'employe_id',
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

}
