<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TauxHeuresSup extends Model
{
    protected $table = 'taux_heures_sup';
    
    protected $fillable = [
        'societe_id',
        'nom',
        'seuil_debut',
        'seuil_fin',
        'taux'
    ];

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
}
