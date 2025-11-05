<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Td extends Model
{
    use HasFactory;

    protected $fillable = [
        'epreuve_id',
        'enseignant_id',
        'titre',
        'description',
        'statut',
        'montant',
        'date_debut',
        'date_fin',
    ];

    public function epreuve()
    {
        return $this->belongsTo(Epreuve::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'td_id');
    }
}
