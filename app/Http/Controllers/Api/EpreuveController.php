<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEpreuveRequest;
use App\Models\Epreuve;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EpreuveController extends Controller
{
    public function index(Request $request)
    {
        $query = Epreuve::query();
        if ($request->user()->hasRole('enseignant')) {
            $query->where('enseignant_id', $request->user()->id);
        }
        return $query->latest()->paginate(15);
    }

    public function store(StoreEpreuveRequest $request)
    {
        $epreuve = Epreuve::create([
            'enseignant_id' => $request->user()->id,
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'proposee',
        ]);
        return response()->json($epreuve, 201);
    }

    public function show(Epreuve $epreuve)
    {
        $this->authorize('view', $epreuve);
        return $epreuve;
    }

    public function update(Request $request, Epreuve $epreuve)
    {
        $this->authorize('update', $epreuve);
        $data = $request->validate([
            'titre' => ['sometimes','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'statut' => ['sometimes', Rule::in(['proposee','validee','annulee'])],
        ]);
        $epreuve->update($data);
        return $epreuve->refresh();
    }

    public function destroy(Epreuve $epreuve)
    {
        $this->authorize('delete', $epreuve);
        $epreuve->delete();
        return response()->json(['message' => 'Supprimée']);
    }
}
