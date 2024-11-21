<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\Sortie;

class StockController extends Controller
{
    public function retirerStock(Request $request, $produitId)
    {
        $request->validate([
            'quantité' => 'required|integer|min:1',
            'zone_id' => 'nullable|exists:zones,id',
        ]);

        $produit = Produit::findOrFail($produitId);

        if ($produit->quantité < $request->quantité) {
            return response()->json(['message' => 'Quantité insuffisante'], 400);
        }

        $produit->quantité -= $request->quantité;
        $number_redure = $produit->quantité - $request->quantité;
        $produit->save();

        Sortie::create([
            'produit_id' => $produitId,
            'zone_id' => $request->zone_id,
            'quantité' => $request->quantité,
            'number_after_reduce' => $number_redure,
        ]);

        return response()->json(['message' => 'Stock mis à jour', 'produit' => $produit]);
    }
}
