<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;


class Tenant extends Model
{
    use HasFactory, Loggable;
    protected $table = 'tenant';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = null; // pas de colonne date_modification sur cette table

    protected $fillable = [
        'nom',
        'tenant_categorie_id',
    ];

    protected $casts = [
        'date_creation' => 'datetime',
    ];

    public function tenantCategorie()
    {
        return $this->belongsTo(TenantCategorie::class, 'tenant_categorie_id');
    }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'tenant_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'tenant_id');
    }

    public function employes()
    {
        return $this->hasMany(Employe::class, 'tenant_id');
    }

    // Vérifie si ce tenant a accès à un module donné, selon sa catégorie.
    // Usage : $tenant->peutAcceder('ventes')
    public function peutAcceder(string $module): bool
    {
        if (!$this->tenantCategorie) {
            return false;
        }

        return match ($module) {
            'rh'     => $this->tenantCategorie->acces_rh,
            'ventes' => $this->tenantCategorie->acces_ventes,
            'achats' => $this->tenantCategorie->acces_achats,
            default  => false,
        };
    }
}
