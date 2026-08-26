<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class ParametrePaie extends Model
{
    use HasFactory;

    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';

    protected $table = 'parametres_paie';
    protected $fillable = ['tenant_id', 'jour_paiement'];



    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
