<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            // 'role' supprimé : le serveur force désormais le rôle enseignant pour les inscriptions publiques
            'bank_name' => ['nullable','string','max:255'],
            'bank_account' => ['nullable','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'establishment' => ['nullable','string','max:255'],
            'subject' => ['nullable','string','max:255'],
            'classe' => ['nullable','string','max:255'],
            'experience_years' => ['nullable','integer','min:0','max:80'],
            'ifru' => ['nullable','string','max:255'],
        ];
    }
}
