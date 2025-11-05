<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'td_id' => ['required','exists:tds,id'],
            'montant' => ['required','numeric','min:0'],
            'banque' => ['nullable','string','max:255'],
            'reference' => ['nullable','string','max:255'],
            'date_paiement' => ['nullable','date'],
        ];
    }
}
