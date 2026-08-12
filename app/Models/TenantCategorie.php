<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantCategorie extends Model
{
    use HasFactory;

    protected $table = 'tenant_categorie';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null;

    protected $fillable = [
        'nom',
        'description',
        'acces_rh',
        'acces_ventes',
        'acces_achats',
    ];

    protected $casts = [
        'acces_rh' => 'boolean',
        'acces_ventes' => 'boolean',
        'acces_achats' => 'boolean',
        'date_creation' => 'datetime',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'tenant_categorie_id');
    }
}