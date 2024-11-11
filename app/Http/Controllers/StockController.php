<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function retirerStock(Request $request, $produitId)
    {
        $request->validate([
            'quantité' => 'required|integer|min:1',
            'zone_id' => 'required|exists:zones,id'
        ]);

        $produit = Produit::findOrFail($produitId);

        if ($produit->quantité < $request->quantité) {
            return response()->json(['message' => 'Quantité insuffisante'], 400);
        }

        // Mettre à jour la quantité
        $produit->quantité -= $request->quantité;
        $produit->save();

        return response()->json(['message' => 'Stock mis à jour', 'produit' => $produit]);
    }
}
