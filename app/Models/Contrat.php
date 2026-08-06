<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrat extends Model
{
    use HasFactory;
    protected $table = 'contrat';
    const CREATED_AT='date_creation';
    const UPDATED_AT='date_modification';
    protected $fillable = [
        'tenant_id',
        'employe_id',
        'numcontrat',
        'typeContrat',
        'date_debut',
        'date_fin',
        'statut',

        'recreteur',
    ];
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',

        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class,'tenant_id');
    }
    public function employe()
    {
        return $this->belongsTo(Employe::class,'employe_id');
    }
    public function conges()
    {
        return $this->hasMany(Conge::class,'contrat_id');
    }
    public function salaires()
    {
        return $this->hasMany(Salaire::class,'contrat_id');
    }
    public function avancesalaires()
    {
        return $this->hasMany(Avancesalaire::class,'contrat_id');
    }
    public function primes()
    {
        return $this->hasMany(Prime::class,'contrat_id');
    }
    public function duree()
    {
        if (!$this->date_debut || !$this->date_fin) {
        return null;
    }
    return $this->date_debut->diffInMonths($this->date_fin);
    }

}
