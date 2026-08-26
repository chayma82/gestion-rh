<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class Notification extends Model
{
    protected $table = 'notification';

    // La table possède désormais created_at/updated_at (gérés
    // automatiquement par Eloquent) EN PLUS de date_reception/date_lecture,
    // qui restent des champs métier distincts qu'on continue de gérer
    // manuellement (voir marquerCommeLue() et NotificationService::creer()).

    protected $fillable = [
        'tenant_id',
        'utilisateur_id',
        'type',
        'titre',
        'message',
        'lue',
        'date_reception',
        'date_lecture',
        'reference_id',
    ];

    protected $casts = [
        'lue'            => 'boolean',
        'date_reception' => 'datetime',
        'date_lecture'   => 'datetime',
    ];

    public function marquerCommeLue(): void
    {
        $this->update([
            'lue'          => true,
            'date_lecture' => now(),
        ]);
    }

    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    // Icône Font Awesome selon le type — utilisé par le topbar ET le dashboard,
    // pour garantir un rendu identique partout sans dupliquer la logique.
    // Types possibles (voir colonne enum): facture, employe, contrat.
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'employe' => 'fa-user-clock',
            'contrat' => 'fa-file-contract',
            'facture' => 'fa-file-invoice-dollar',
            default   => 'fa-bell',
        };
    }

    // Classes Tailwind de couleur associées au type, même principe.
    public function getCouleurAttribute(): string
    {
        return match ($this->type) {
            'employe' => 'bg-orange-50 text-[#E2721B]',
            'contrat' => 'bg-red-50 text-red-500',
            'facture' => 'bg-orange-50 text-[#E2721B]',
            default   => 'bg-gray-100 text-gray-500',
        };
    }
}
