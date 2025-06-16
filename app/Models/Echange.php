<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Echange extends Model
{
    use HasFactory;

    protected $table = 'echanges';
    
    protected $fillable = [
        'employe_id',
        'target_employe_id',
        'date',
        'target_date',
        'status',
        'societe_id',
        'commentaire'
    ];

    protected $casts = [
        'date' => 'date',
        'target_date' => 'date',
    ];

    /**
     * Relation avec l'employé qui demande l'échange
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    /**
     * Relation avec l'employé cible de l'échange
     */
    public function targetEmploye()
    {
        return $this->belongsTo(Employe::class, 'target_employe_id');
    }

    /**
     * Relation avec la société
     */
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
}
