<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Liste tous les utilisateurs
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // Créer un nouvel utilisateur
    public function store(Request $request)
    {
        // Validation des champs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'is_admin' => 'nullable|boolean',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation du fichier
        ]);

        // Hash du mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Sauvegarde de la photo de profil
        if ($request->hasFile('photo_profil')) {
            $image = $request->file('photo_profil');
            $filename = 'profile_photos/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/profile_photos'), $filename);

            // Sauvegarde du chemin relatif dans la base
            $validated['photo_profil'] = 'storage/' . $filename;
        }

        // Création de l'utilisateur dans la base de données
        $user = User::create($validated);

        Log::info('Utilisateur créé avec succès : ', $user->toArray());
        return response()->json($user, 201);
    }

    // Affiche un utilisateur spécifique
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Met à jour un utilisateur existant
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de la photo de profil
        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne image si elle existe
            if ($user->photo_profil && file_exists(public_path($user->photo_profil))) {
                unlink(public_path($user->photo_profil));
            }

            $image = $request->file('photo_profil');
            $filename = 'profile_photos/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Copie manuelle dans public/storage
            $image->move(public_path('storage/profile_photos'), $filename);

            // Sauvegarde du chemin relatif dans la base
            $validated['photo_profil'] = 'storage/' . $filename;
        }

        // Mise à jour du mot de passe
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        Log::info('Utilisateur mis à jour avec succès : ', $user->toArray());
        return response()->json($user);
    }

    // Supprime un utilisateur
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Supprimer la photo de profil associée si elle existe
        if ($user->photo_profil && file_exists(public_path($user->photo_profil))) {
            unlink(public_path($user->photo_profil));
        }

        $user->delete();

        Log::info('Utilisateur supprimé avec succès : ', ['id' => $id]);
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
