<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'taux_horaire',
        'description',
        'societe_id'
    ];

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
}
