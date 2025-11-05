<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Td;
use App\Models\Epreuve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function enseignants()
    {
        $users = User::role('enseignant')
            ->select('id','name','email','is_validated','bank_name','bank_account','phone','establishment','subject','classe','ifru','created_at')
            ->orderBy('name')->get();
        return response()->json($users);
    }

    public function deleteEnseignant(User $user)
    {
        if ($user->role !== 'enseignant') {
            return response()->json(['message' => 'Pas un enseignant'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Supprimé']);
    }
    public function validerEnseignant(User $user)
    {
        if ($user->role !== 'enseignant') {
            return response()->json(['message' => 'Utilisateur non enseignant'], 422);
        }
        $user->update(['is_validated' => true]);
        return response()->json(['message' => 'Compte validé', 'user' => $user]);
    }

    public function createComptable(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'is_validated' => true,
            'role' => 'comptable',
        ]);
        $user->assignRole('comptable');
        return response()->json($user, 201);
    }

    public function stats(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);

    $usersTotal = User::where('role','!=','admin')->count();
        $enseignantsValides = User::role('enseignant')->where('is_validated', true)->count();
        $enseignantsEnAttente = User::role('enseignant')->where('is_validated', false)->count();
        $tdsTotal = Td::count();
        $tdsEnCours = Td::where('statut','en_cours')->count();
        $tdsTermine = Td::where('statut','termine')->count();
        $tdsPaye = Td::where('statut','paye')->count();
        $epreuvesTotal = Epreuve::count();

        // Activités récentes (fusion simplifiée)
        $recentUsers = User::latest()->take(5)->get()->map(fn($u)=>[
            'type' => 'user',
            'message' => "Utilisateur {$u->name} créé",
            'created_at' => $u->created_at,
        ]);
        $recentTds = Td::latest()->take(5)->get()->map(fn($t)=>[
            'type' => 'td',
            'message' => "TD {$t->titre} ({$t->statut})",
            'created_at' => $t->created_at,
        ]);
        $recentEpreuves = Epreuve::latest()->take(5)->get()->map(fn($e)=>[
            'type' => 'epreuve',
            'message' => "Épreuve {$e->titre} créée",
            'created_at' => $e->created_at,
        ]);
        $recent = $recentUsers->concat($recentTds)->concat($recentEpreuves)
            ->sortByDesc('created_at')->values()->take(10)->map(function($item){
                $item['created_at_diff'] = $item['created_at']->diffForHumans();
                return $item;
            })->all();

        return response()->json([
            'users_total' => $usersTotal,
            'enseignants_valides' => $enseignantsValides,
            'enseignants_en_attente' => $enseignantsEnAttente,
            'tds_total' => $tdsTotal,
            'tds_en_cours' => $tdsEnCours,
            'tds_termine' => $tdsTermine,
            'tds_paye' => $tdsPaye,
            'epreuves_total' => $epreuvesTotal,
            'recent' => $recent,
        ]);
    }

    public function updateProfile(Request $request)
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $request->user()->update($data);
        return response()->json($request->user());
    }
}
