<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTdRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'epreuve_id' => ['required','exists:epreuves,id'],
            'enseignant_id' => ['required','exists:users,id'],
            'titre' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'montant' => ['required','numeric','min:0'],
            'date_debut' => ['nullable','date'],
            'date_fin' => ['nullable','date','after_or_equal:date_debut'],
        ];
    }
}
