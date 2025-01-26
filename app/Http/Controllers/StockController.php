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
            'personne_id' => 'nullable|exists:zones,id',
            'observation' => 'nullable|string',
        ]);

        // Récupérer le produit à partir de l'ID
        $produit = Produit::findOrFail($produitId);

        // Vérifier si la quantité demandée est disponible
        if ($produit->quantité < $request->quantité) {
            return response()->json(['message' => 'Quantité insuffisante'], 400);
        }

        // Calcul du stock après la sortie
        $produit->quantité -= $request->quantité;

        // Sauvegarder le produit avec la nouvelle quantité
        $produit->save();

        // Créer une sortie pour enregistrer l'opération
        Sortie::create([
            'produit_id' => $produitId,
            'zone_id' => $request->zone_id,
            'personne_id' => $request->personne_id,
            'quantité' => $request->quantité,
            'number_after_reduce' => $produit->quantité, // Stock restant après la sortie
            'observation' => $request->observation,
        ]);

        // Retourner une réponse de succès
        return response()->json(['message' => 'Stock mis à jour', 'produit' => $produit]);
    }


    public function supprimerSortie($sortieId)
    {
        $sortie = Sortie::findOrFail($sortieId);
        $produit = Produit::findOrFail($sortie->produit_id);

        $produit->quantité += $sortie->quantité;

        $produit->save();

        $sortie->delete();

        return response()->json([
            'message' => 'Sortie supprimée et stock réincrémenté avec succès.',
            'produit' => $produit,
        ]);
    }
}
