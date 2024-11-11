<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

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
            'image_url' => 'nullable|string',
        ]);

        $zone = Zone::create($validated);
        return response()->json($zone, 201);
    }

    // Affiche une zone spécifique
    public function show($id)
    {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
    }

    // Met à jour une zone spécifique
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'image_url' => 'nullable|string',
        ]);

        $zone->update($validated);
        return response()->json($zone);
    }

    // Supprime une zone spécifique
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zone supprimée avec succès']);
    }
}
