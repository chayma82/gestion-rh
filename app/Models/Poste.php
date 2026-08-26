<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Loggable;

class Poste extends Model
{
    use HasFactory, Loggable;
    protected $table = 'poste';

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

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'poste_id');
    }
}
