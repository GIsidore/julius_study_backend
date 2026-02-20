<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:eleves,email',
            'mot_de_passe' => 'required|string|min:6',
            'niveau_id' => 'required|exists:niveaux,id',
            'date_naissance' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $eleve = Eleve::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'niveau_id' => $request->niveau_id,
            'date_naissance' => $request->date_naissance,
            'langue' => $request->langue ?? 'fr',
        ]);

        $token = $eleve->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $eleve->load('niveau'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'mot_de_passe' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $eleve = Eleve::where('email', $request->email)->first();

        if (! $eleve || ! Hash::check($request->mot_de_passe, $eleve->mot_de_passe)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect',
            ], 401);
        }

        $eleve->update(['derniere_connexion' => now()]);

        $token = $eleve->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $eleve->load('niveau'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('niveau'));
    }
}
