<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    // Liste tous les produits
    public function index()
    {
        $produits = Produit::all();
        return response()->json($produits);
    }

    // Créer un nouveau produit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantité' => 'required|integer',
            'image_url' => 'nullable|string',
            'file_product' => 'nullable|string',
        ]);

        $produit = Produit::create($validated);
        return response()->json($produit, 201);
    }

    // Affiche un produit spécifique
    public function show($id)
    {
        $produit = Produit::findOrFail($id);
        return response()->json($produit);
    }

    // Met à jour un produit spécifique
    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'quantité' => 'sometimes|integer',
            'image_url' => 'nullable|string',
            'file_product' => 'nullable|string',
        ]);

        $produit->update($validated);
        return response()->json($produit);
    }

    // Supprime un produit spécifique
    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);
        $produit->delete();

        return response()->json(['message' => 'Produit supprimé avec succès']);
    }
}
