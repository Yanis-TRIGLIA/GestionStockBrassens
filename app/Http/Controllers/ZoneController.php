<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZoneController extends Controller
{
    // Liste toutes les zones
    public function index()
    {
        $zones = Zone::all();
        return response()->json($zones);
    }

    // Créer une nouvelle zone
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'nullable|string|in:zone,personne',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Validation de l'image
        ]);

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('zones/images', 'public');
        }

        $zone = Zone::create($validated);

        return response()->json($zone, 201);
    }

    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'type' => 'nullable|string|in:zone,personne',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048', // Validation de l'image
        ]);

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($zone->image_url) {
                Storage::disk('public')->delete($zone->image_url);
            }

            // Stocker la nouvelle image
            $validated['image_url'] = $request->file('image')->store('zones/images', 'public');
        }

        $zone->update($validated);

        return response()->json($zone);
    }

    // Affiche une zone spécifique
    public function show($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    // Supprime une zone spécifique
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);

        // Supprimer l'image associée si elle existe
        if ($zone->image_url) {
            Storage::disk('public')->delete($zone->image_url);
        }

        $zone->delete();

        return response()->json(['message' => 'Zone supprimée avec succès']);
    }
}
