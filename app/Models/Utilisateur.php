<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'utilisateur';

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $fillable = [
        'tenant_id',
        'entreprise_id',
        'role_id',
        'nom',
        'prenom',
        'email',
        'motdepasse',
        'telephone',
        'actif',
    ];

    protected $hidden = [
        'motdepasse',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime',
    ];

    /**
     * Laravel s'attend par défaut à une colonne "password".
     * On indique ici que le mot de passe est stocké dans "motdepasse".
     */
    public function getAuthPassword()
    {
        return $this->motdepasse;
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function employesCrees()
    {
        return $this->hasMany(Employe::class, 'utilisateur_creation_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'utilisateur_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'utilisateur_id');
    }

}
