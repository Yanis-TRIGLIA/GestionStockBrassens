<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriesController
{

    // Liste toutes les categories
    public function index()
    {
        $categories = Categories::all();
        return response()->json($categories);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories/images', 'public');
        }

        $categorie = Categories::create($validated);

        return response()->json($categorie, 201);
    }

    // Mise à jour d'un categorie
    public function update(Request $request, $id)
    {
        $categorie = Categories::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($categorie->image_url) {
                Storage::disk('public')->delete($categorie->image_url);
            }

            // Stocker la nouvelle image
            $validated['image'] = $request->file('image')->store('categories/images', 'public');
        }


        $categorie->update($validated);


        return response()->json($categorie);
    }


    // Affiche une categorie spécifique
    public function show($id)
    {
        $categorie = Categories::findOrFail($id);
        return response()->json($categorie);
    }

    // Supprime une categorie spécifique
    public function destroy($id)
    {
        $categorie = Categories::findOrFail($id);

        // Supprimer l'image associée si elle existe
        if ($categorie->image_url) {
            Storage::disk('public')->delete($categorie->image_url);
        }

        $categorie->delete();

        return response()->json(['message' => 'Categories supprimée avec succès']);
    }

}
