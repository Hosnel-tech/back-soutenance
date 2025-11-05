<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Paiement;
use App\Models\Td;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $query = Paiement::with('td.enseignant');
        if ($banque = $request->query('banque')) {
            $query->where('banque', $banque);
        }
        return $query->latest()->paginate(20);
    }

    public function store(StorePaiementRequest $request)
    {
        $data = $request->validated();
        $paiement = Paiement::create($data);
        $td = Td::findOrFail($data['td_id']);
        if ($td->statut !== 'paye') {
            $td->update(['statut' => 'paye']);
            $td->enseignant->notify(new \App\Notifications\PaiementEffectueNotification($td));
        }
        return response()->json($paiement, 201);
    }

    public function exportPdf(Request $request)
    {
        $query = Paiement::with('td.enseignant');
        if ($banque = $request->query('banque')) {
            $query->where('banque', $banque);
        }
        $paiements = $query->orderBy('date_paiement','desc')->get();
        $pdf = Pdf::loadView('pdf.paiements', compact('paiements'));
        return $pdf->download('rapport_paiements.pdf');
    }
}
