<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaiementFactureVente extends Model
{
    protected $table = 'paiement_facture_vente';

    protected $casts = [
        'date_paiement' => 'date',
    ];

    protected $fillable = [
        'facture_id', 'montant', 'methode_paiement', 'date_paiement', 'numero_quittance',
    ];

    public function facture()
    {
        return $this->belongsTo(FactureVente::class, 'facture_id');
    }
}
