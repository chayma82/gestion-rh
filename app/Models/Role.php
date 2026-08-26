<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class Role extends Model
{
    use HasFactory, Loggable;
    protected $table = 'role';

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'nom',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'role_id');
    }
}
