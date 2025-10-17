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
    // Crée un nouveau produit
public function store(Request $request)
{
    // Log des données reçues
    Log::info('Données reçues : ', $request->all());

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
            'prix' => 'nullable|numeric',
            'reference' => 'nullable|string|unique:produits,reference',
        ]);

        // Gestion des fichiers
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'images/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/images'), $filename);

            // Sauvegarde du chemin dans la base
            $validated['image_url'] = 'storage/' . $filename;
        }

        $fichesTechniquesPaths = [];
        if ($request->hasFile('fiches_techniques')) {
            foreach ($request->file('fiches_techniques') as $file) {
                $filename = 'files/' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Copie manuelle dans public/storage
                $file->move(public_path('storage/files'), $filename);

                $fichesTechniquesPaths[] = 'storage/' . $filename;
            }
        }

        // Création du produit
        $produit = Produit::create([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'quantité' => $validated['quantité'],
            'image_url' => $validated['image_url'] ?? null,
            'file_product' => json_encode($fichesTechniquesPaths),
            'prix' => $validated['prix']?? null,
            'reference' => $validated['reference']?? null,

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
        'prix' => 'nullable|numeric',
        'reference' => 'nullable|string|unique:produits,reference,'. $id,
    ]);

    // Mise à jour des fichiers
    if ($request->hasFile('image')) {
        // Supprimer l'image précédente
        if ($produit->image_url && file_exists(public_path($produit->image_url))) {
            unlink(public_path($produit->image_url));
        }

        $image = $request->file('image');
        $filename = 'images/' . uniqid() . '.' . $image->getClientOriginalExtension();

        // Copie dans public/storage
        $image->move(public_path('storage/images'), $filename);
        $validated['image_url'] = 'storage/' . $filename;
    }

    if ($request->hasFile('fiches_techniques')) {
        $fichesTechniquesPaths = [];
        foreach ($request->file('fiches_techniques') as $file) {
            $filename = 'files/' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Copie manuelle
            $file->move(public_path('storage/files'), $filename);
            $fichesTechniquesPaths[] = 'storage/' . $filename;
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

    // Supprime un fichier spécifique rattaché au produit (fichier dans file_product)
    public function deleteFile(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);
        $file = $request->input('file');
        if (!$file) {
            return response()->json(['error' => 'Aucun fichier spécifié'], 400);
        }

        $files = json_decode($produit->file_product ?? '[]', true);
        if (!in_array($file, $files)) {
            return response()->json(['error' => 'Fichier introuvable'], 404);
        }

        // Supprimer le fichier physique si présent
        $publicPath = public_path($file);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
        }

        // Retirer du tableau et sauvegarder
        $files = array_values(array_filter($files, function ($f) use ($file) {
            return $f !== $file;
        }));
        $produit->file_product = json_encode($files);
        $produit->save();

        return response()->json(['message' => 'Fichier supprimé']);
    }
}
