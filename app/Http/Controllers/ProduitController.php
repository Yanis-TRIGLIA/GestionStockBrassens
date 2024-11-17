<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        return response()->json($produits);
    }



    public function show($id)
    {
        $produit = Produit::findOrFail($id);
        return response()->json($produit);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantité' => 'required|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'file_product' => 'nullable|file|mimes:pdf',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('images', 'public');
        }

        if ($request->hasFile('file_product')) {
            $file = $request->file('file_product');
            $path = $file->store('files', 'public');
            $validated['file_product'] = $path;
        }


        $produit = Produit::create($validated);
        return response()->json($produit, 201);
    }

    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);


        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'quantité' => 'sometimes|integer',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'file_product' => 'nullable|file|mimes:pdf|max:5120',
        ]);

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
