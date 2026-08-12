<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Hash automatiquement le mot de passe à chaque affectation
     * (Utilisateur::create([...]) ou $utilisateur->motdepasse = '...').
     * Évite un double hash si la valeur est déjà un hash bcrypt/argon.
     */
    public function setMotdepasseAttribute(?string $value): void
    {
        if (empty($value)) {
            return;
        }

        // Si la valeur est déjà un hash bcrypt/argon (ex: ressemencement, import),
        // on ne la re-hash pas. Sinon on hash le mot de passe en clair.
        $dejaHash = str_starts_with($value, '$2y$') || str_starts_with($value, '$argon2');

        $this->attributes['motdepasse'] = $dejaHash ? $value : Hash::make($value);
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