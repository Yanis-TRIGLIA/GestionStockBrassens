<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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

        // Sauvegarde du fichier photo_profil si présent
        if ($request->hasFile('photo_profil')) {
            $photoPath = $request->file('photo_profil')->store('profile_photos', 'public');
            $validated['photo_profil'] = $photoPath;
        }

        // Création de l'utilisateur dans la base de données
        $user = User::create($validated);

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

        if ($request->hasFile('photo_profil')) {
            // Supprimer l'ancienne image si elle existe
            if ($user->photo_profil && Storage::exists('public/' . $user->photo_profil)) {
                Storage::delete('public/' . $user->photo_profil);
            }

            $validated['photo_profil'] = $request->file('photo_profil')->store('profiles', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }


    // Supprime un utilisateur
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->photo_profil && Storage::exists('public/' . $user->photo_profil)) {
            Storage::delete('public/' . $user->photo_profil);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
