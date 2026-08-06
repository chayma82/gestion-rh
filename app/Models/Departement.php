<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departement extends Model
{
    use HasFactory;

    protected $table = 'departement';

    // Pas de date_creation/date_modification dans cette table
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'code',
        'libelle',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function postes()
    {
        return $this->hasMany(Poste::class, 'departement_id');
    }

    public function employes()
    {
        return $this->hasMany(Employe::class, 'departement_id');
    }
}
