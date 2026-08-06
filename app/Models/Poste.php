<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poste extends Model
{
    use HasFactory;

    protected $table = 'poste';

    // Pas de date_creation/date_modification dans cette table
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'departement_id',
        'code',
        'libelle',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function employes()
    {
        return $this->hasMany(Employe::class, 'poste_id');
    }
}
