<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $image = $request->file('image');
            $filename = 'categories/images/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/categories/images'), $filename);

            // Sauvegarde du chemin dans la base
            $validated['image'] = 'storage/' . $filename;
        }

        $categorie = Categories::create($validated);

        Log::info('Catégorie créée avec succès : ', $categorie->toArray());
        return response()->json($categorie, 201);
    }

    // Mise à jour d'une catégorie
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
            if ($categorie->image && file_exists(public_path($categorie->image))) {
                unlink(public_path($categorie->image));
            }

            $image = $request->file('image');
            $filename = 'categories/images/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/categories/images'), $filename);

            // Sauvegarde du nouveau chemin
            $validated['image'] = 'storage/' . $filename;
        }

        $categorie->update($validated);

        Log::info('Catégorie mise à jour avec succès : ', $categorie->toArray());
        return response()->json($categorie);
    }

    // Affiche une catégorie spécifique
    public function show($id)
    {
        $categorie = Categories::findOrFail($id);
        return response()->json($categorie);
    }

    // Supprime une catégorie spécifique
    public function destroy($id)
    {
        $categorie = Categories::findOrFail($id);

        // Supprimer l'image associée si elle existe
        if ($categorie->image && file_exists(public_path($categorie->image))) {
            unlink(public_path($categorie->image));
        }

        $categorie->delete();

        Log::info('Catégorie supprimée avec succès : ', ['id' => $id]);
        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }
}
