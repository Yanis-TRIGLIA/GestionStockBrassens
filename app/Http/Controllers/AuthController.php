<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Inscription d'un nouvel utilisateur (seul l'admin peut le faire)
    public function register(Request $request)
    {
        // Vérification si l'utilisateur est bien un admin
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Validation des champs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'sometimes|boolean', // Facultatif, par défaut false
            'role' => 'nullable|string|max:255', // Facultatif, pour le rôle de l'utilisateur
            'photo_profil' => 'nullable|string', // URL de la photo de profil, facultatif
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'] ?? false, 
            'role' => $validated['role'] ?? null,
            'photo_profil' => $validated['photo_profil'] ?? null,
        ]);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
    }

    // Connexion de l'utilisateur
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    // Déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie et tokens révoqués']);
    }
}
