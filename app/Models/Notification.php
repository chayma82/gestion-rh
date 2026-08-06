<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

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
}
