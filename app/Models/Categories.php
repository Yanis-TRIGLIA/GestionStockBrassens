<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'image',
    ];

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'categorie_produit');
    }

}
