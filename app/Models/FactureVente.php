<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class FactureVente extends Model
{
    use  Loggable;
    protected $table = 'facture_vente';

    protected $casts = [
        'dateEmissionFacture' => 'date',
        'date_echeance'       => 'date',
    ];

    protected $fillable = [
        'tenant_id', 'entreprise_id', 'client_id', 'numFacture', 'nom_client',
        'dateEmissionFacture', 'date_echeance', 'montant_ht', 'montant_tva',
        'montant_ttc', 'montant_paye', 'montant_restant', 'statut','statut_avant_archivage',
        'chemin_pdf', 'nom_pdf',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function details()
    {
        return $this->hasMany(DetailFactureVente::class, 'facture_id');
    }

    public function paiements()
{
    return $this->hasMany(PaiementFactureVente::class, 'facture_id')
        ->orderByDesc('date_paiement')
        ->orderByDesc('id');
}

    public function scopeActives($query)
    {
        return $query->where('statut', '!=', 'archive');
    }

    public function scopeArchivees($query)
    {
        return $query->where('statut', 'archive');
    }
    public function getStatutAfficheAttribute()
    {
        if (in_array($this->statut, ['archive', 'payee'])) {
            return $this->statut;
        }

        if ($this->date_echeance && $this->date_echeance->lt(now()->startOfDay())) {
            return 'en_retard';
        }

        return 'en_attente';
    }

}
