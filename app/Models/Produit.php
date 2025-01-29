<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'quantité',
        'image_url',
        'file_product',
        'prix',      
        'reference'   
    ];

    // Dans le modèle Produit
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'categorie_produit', 'produit_id', 'categorie_id');
    }

    public function paniers()
    {
        return $this->belongsToMany(Panier::class, 'panier_produit')
            ->withPivot('quantite')
            ->withTimestamps();
    }
}
