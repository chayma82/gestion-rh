<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailFactureAchat extends Model
{
    protected $table = 'detail_facture_achat';
    public $timestamps = false;

    protected $fillable = [
        'facture_id', 'reference_produit', 'description', 'quantite', 'prix_unitaire', 'montant_ligne',
    ];

    public function facture()
    {
        return $this->belongsTo(FactureAchat::class, 'facture_id');
    }
}
