<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class FactureAchat extends Model
{
    use  Loggable;
    protected $table = 'facture_achat';

    protected $casts = [
        'dateEmissionFacture' => 'date',
        'date_echeance'       => 'date',
    ];

    protected $fillable = [
        'tenant_id', 'entreprise_id', 'fournisseur_id', 'numFacture',
        'dateEmissionFacture', 'date_echeance', 'montant_ht', 'montant_tva',
        'montant_ttc', 'montant_paye', 'statut','statut_avant_archivage', 'chemin_pdf', 'nom_pdf',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function details()
    {
        return $this->hasMany(DetailFactureAchat::class, 'facture_id');
    }

    public function scopeActives($query)
    {
        return $query->where('statut', '!=', 'archive');
    }

    public function scopeArchivees($query)
    {
        return $query->where('statut', 'archive');
    }

}
