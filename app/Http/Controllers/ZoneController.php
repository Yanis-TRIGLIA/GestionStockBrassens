<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $image = $request->file('image');
            $filename = 'zones/images/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/zones/images'), $filename);

            // Sauvegarde du chemin dans la base
            $validated['image_url'] = 'storage/' . $filename;
        }

        $zone = Zone::create($validated);

        Log::info('Zone créée avec succès : ', $zone->toArray());
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
            if ($zone->image_url && file_exists(public_path($zone->image_url))) {
                unlink(public_path($zone->image_url));
            }

            $image = $request->file('image');
            $filename = 'zones/images/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/zones/images'), $filename);

            // Sauvegarde du chemin dans la base
            $validated['image_url'] = 'storage/' . $filename;
        }

        $zone->update($validated);

        Log::info('Zone mise à jour avec succès : ', $zone->toArray());
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
        if ($zone->image_url && file_exists(public_path($zone->image_url))) {
            unlink(public_path($zone->image_url));
        }

        $zone->delete();

        Log::info('Zone supprimée avec succès : ', ['id' => $id]);
        return response()->json(['message' => 'Zone supprimée avec succès']);
    }
}
