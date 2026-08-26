<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

/**
 * Représente un token de réinitialisation de mot de passe en attente.
 *
 * Pas d'auto-incrément ni de clé primaire classique sur cette table
 * (comme le fait Laravel par défaut pour password_reset_tokens) :
 * l'email sert de clé de recherche, et created_at gère l'expiration.
 *
 * Placez ce fichier dans : app/Models/PasswordResetUtilisateur.php
 */
class PasswordResetUtilisateur extends Model
{
    use  Loggable;
    protected $table = 'password_reset_utilisateur';

    // Pas de colonne "id" sur cette table
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';

    // La table ne gère que created_at (pas de colonne updated_at)
    const UPDATED_AT = null;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
