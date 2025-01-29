<?php

namespace App\Http\Controllers;

use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanierController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $panier = $user->panier()->with('produits')->first();

        if (!$panier) {
            return response()->json(['message' => 'Le panier est vide.'], 200);
        }

        return response()->json($panier);
    }

    public function addProduct(Request $request)
    {
        $user = Auth::user();
        $panier = $user->panier ?: Panier::create(['user_id' => $user->id]);

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1'
        ]);

        $produit = Produit::findOrFail($request->produit_id);

        $exists = $panier->produits()->where('produit_id', $produit->id)->exists();

        if (!$exists) {
            $panier->produits()->attach($produit->id, ['quantite' => $request->quantite]);
            return response()->json(['message' => 'Produit ajouté au panier.'], 201);
        }

        return response()->json(['message' => 'Le produit est déjà dans le panier.'], 400);
    }

    public function removeProduct($produitId)
    {
        $user = Auth::user();
        $panier = $user->panier;
    
        if (!$panier) {
            return response()->json(['message' => 'Le panier est vide.'], 400);
        }
    
        $panier->produits()->detach($produitId);
        return response()->json(['message' => 'Produit retiré du panier.']);
    }
    

    public function updateProductQuantity(Request $request, $produitId)
    {
        $user = Auth::user();
        $panier = $user->panier;

        if (!$panier) {
            return response()->json(['message' => 'Le panier est vide.'], 400);
        }

        $request->validate([
            'quantite' => 'required|integer|min:1'
        ]);

        if (!$panier->produits()->where('produit_id', $produitId)->exists()) {
            return response()->json(['message' => 'Le produit n\'existe pas dans le panier.'], 404);
        }

        $panier->produits()->updateExistingPivot($produitId, ['quantite' => $request->quantite]);

        return response()->json(['message' => 'Quantité mise à jour.', 'panier' => $panier->load('produits')]);
    }


    public function clear()
    {
        $user = Auth::user();
        $panier = $user->panier;

        if (!$panier) {
            return response()->json(['message' => 'Le panier est déjà vide.'], 400);
        }

        $panier->produits()->detach();
        return response()->json(['message' => 'Le panier a été vidé.']);
    }
}
