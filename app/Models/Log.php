<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Journal d'audit : trace les actions importantes effectuées dans l'app
 * (connexion, création, validation, activation, suppression, etc.).
 *
 * La table n'a qu'une colonne de date (date_action), pas de couple
 * created_at/updated_at classique : on désactive donc les timestamps
 * automatiques de Laravel et on gère date_action nous-mêmes.
 *
 * Placez ce fichier dans : app/Models/Log.php
 */
class Log extends Model
{
    protected $table = 'log';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'utilisateur_id',
        'record_id',
        'nom_table',
        'description',
        'date_action',
    ];

    protected $casts = [
        'date_action' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Raccourci pour créer une entrée de log en une ligne, depuis
     * n'importe quel contrôleur.
     *
     * Exemple :
     *   Log::enregistrer(
     *       tenantId: $utilisateur->tenant_id,
     *       utilisateurId: $utilisateur->id,
     *       recordId: $utilisateur->id,
     *       nomTable: 'utilisateur',
     *       description: "Connexion réussie de {$utilisateur->email}",
     *   );
     */
    public static function enregistrer(
        ?int $tenantId,
        ?int $utilisateurId,
        ?int $recordId,
        string $nomTable,
        string $description
    ): self {
        return self::create([
            'tenant_id'      => $tenantId,
            'utilisateur_id' => $utilisateurId,
            'record_id'      => $recordId,
            'nom_table'      => $nomTable,
            'description'    => $description,
            'date_action'    => now(),
        ]);
    }
}
