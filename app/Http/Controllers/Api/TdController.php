<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTdRequest;
use App\Models\Td;
use App\Models\Epreuve;
use App\Notifications\TdCreeNotification;
use App\Notifications\PaiementEffectueNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TdController extends Controller
{
    public function index(Request $request)
    {
        $query = Td::with(['epreuve','enseignant']);
        if ($request->user()->hasRole('enseignant')) {
            $query->where('enseignant_id', $request->user()->id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->get('statut'));
        }
        return $query->latest()->paginate(15);
    }

    public function store(StoreTdRequest $request)
    {
        $this->authorize('create', Td::class);
        $data = $request->validated();
        $td = Td::create([
            ...$data,
            'statut' => 'en_cours',
        ]);
        $td->enseignant->notify(new TdCreeNotification($td));
        return response()->json($td, 201);
    }

    public function terminer(Request $request, Td $td)
    {
        $this->authorize('update', $td);
        if ($td->statut !== 'en_cours') {
            return response()->json(['message' => 'TD non en cours'], 422);
        }
        $td->update(['statut' => 'termine']);
        return response()->json($td);
    }

    public function marquerPaye(Request $request, Td $td)
    {
        $this->authorize('update', $td);
        $td->update(['statut' => 'paye']);
        $td->enseignant->notify(new PaiementEffectueNotification($td));
        return response()->json($td);
    }

    public function update(Request $request, Td $td)
    {
        $this->authorize('update', $td);
        $data = $request->validate([
            'titre' => ['sometimes','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'date_debut' => ['sometimes','date'],
            'date_fin' => ['sometimes','date','after_or_equal:date_debut'],
            'montant' => ['sometimes','numeric','min:0'],
            'statut' => ['sometimes', Rule::in(['en_cours','termine','paye'])],
        ]);
        // Ne pas permettre de revenir en arrière sur statut payé
        if (isset($data['statut']) && $td->statut === 'paye' && $data['statut'] !== 'paye') {
            unset($data['statut']);
        }
        $td->update($data);
        return $td->refresh()->load(['epreuve','enseignant']);
    }

    public function destroy(Request $request, Td $td)
    {
        $this->authorize('update', $td); // même règle que update
        if ($td->statut === 'paye') {
            return response()->json(['message' => 'Impossible de supprimer un TD payé'], 422);
        }
        $td->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}
