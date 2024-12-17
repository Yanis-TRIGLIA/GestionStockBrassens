<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProduitController extends Controller
{
    // Affiche tous les produits avec leurs catégories
    public function index()
    {
        $produits = Produit::with('categories')->get();
        return response()->json($produits);
    }

    // Affiche un produit spécifique avec ses catégories
    public function show($id)
    {
        $produit = Produit::with('categories')->findOrFail($id);
        return response()->json($produit);
    }

    // Crée un nouveau produit
    public function store(Request $request)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantité' => 'required|integer',
                'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
                'fiches_techniques.*' => 'nullable|file|mimes:pdf|max:5120',
                'categories' => 'required|array',
                'categories.*' => 'exists:categories,id',
            ]);

            Log::info('Validation réussie', $validated);

            if ($request->hasFile('image')) {
                $validated['image_url'] = $request->file('image')->store('images', 'public');
                Log::info('Image ajoutée : ' . $validated['image_url']);
            }

            $fichesTechniquesPaths = [];
            if ($request->hasFile('fiches_techniques')) {
                foreach ($request->file('fiches_techniques') as $file) {
                    $path = $file->store('files', 'public');
                    $fichesTechniquesPaths[] = $path;
                    Log::info('Fiche technique ajoutée : ' . $path);
                }
            }

            // Création du produit
            $produit = Produit::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'] ?? null,
                'quantité' => $validated['quantité'],
                'image_url' => $validated['image_url'] ?? null,
                'file_product' => json_encode($fichesTechniquesPaths),
            ]);

            Log::info('Produit créé avec succès : ', $produit->toArray());

            // Association des catégories
            $produit->categories()->sync($validated['categories']);
            Log::info('Catégories associées : ', $validated['categories']);

            return response()->json($produit->load('categories'), 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du produit : ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création du produit.'], 500);
        }
    }


    // Met à jour un produit existant
    // Met à jour un produit existant
    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'quantité' => 'sometimes|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'file_product' => 'nullable|file|mimes:pdf|max:5120',
            'fiches_techniques' => 'nullable|array', 
            'fiches_techniques.*' => 'file|mimes:pdf|max:5120',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Mise à jour des fichiers
        if ($request->hasFile('image')) {
            if ($produit->image_url) {
                Storage::disk('public')->delete($produit->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('images', 'public');
        }

        if ($request->hasFile('file_product')) {
            if ($produit->file_product) {
                Storage::disk('public')->delete($produit->file_product);
            }
            $validated['file_product'] = $request->file('file_product')->store('files', 'public');
        }

        // Mise à jour des fiches techniques
        if ($request->hasFile('fiches_techniques')) {
            $fichesTechniquesPaths = [];
            foreach ($request->file('fiches_techniques') as $file) {
                $path = $file->store('files', 'public');
                $fichesTechniquesPaths[] = $path;
            }
            $validated['file_product'] = json_encode($fichesTechniquesPaths); 
        }

        $produit->update($validated);

        $produit->categories()->sync($validated['categories']);

        Log::info('Produit mis à jour avec succès : ', $produit->toArray());
        return response()->json($produit->load('categories'));
    }


    // Supprime un produit spécifique
    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);

        // Supprimer les fichiers associés s'ils existent
        if ($produit->image_url) {
            Storage::disk('public')->delete($produit->image_url);
        }

        if ($produit->file_product) {
            Storage::disk('public')->delete($produit->file_product);
        }

        // Supprimer le produit
        $produit->delete();

        Log::info('Produit supprimé avec succès : ', ['id' => $id]);
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }
}
