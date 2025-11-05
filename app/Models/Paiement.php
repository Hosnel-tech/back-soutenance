<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'td_id', 'montant', 'banque', 'reference', 'date_paiement'
    ];

    public function td()
    {
        return $this->belongsTo(Td::class);
    }
}
