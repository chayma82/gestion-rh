<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Loggable;

class DetailFactureVente extends Model
{
    protected $table = 'detail_facture_vente';
    public $timestamps = false;

    protected $fillable = [
        'facture_id', 'reference', 'designation', 'description',
        'quantite', 'unite', 'prix_unitaire', 'taux_tva', 'montant_ligne',
    ];

    public function facture()
    {
        return $this->belongsTo(FactureVente::class, 'facture_id');
    }
}
