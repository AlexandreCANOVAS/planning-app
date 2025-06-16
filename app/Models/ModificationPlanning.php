<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employe;
use App\Models\Planning;
use App\Models\Lieu;

class ModificationPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'planning_id',
        'type_modification',
        'date_demande',
        'statut',
        'motif',
        'details',
        'nouvelle_date',
        'nouveau_lieu_id',
        'nouvelle_heure_debut',
        'nouvelle_heure_fin'
    ];

    protected $casts = [
        'date_demande' => 'datetime',
        'nouvelle_date' => 'date',
    ];

    /**
     * Récupère l'employé associé à cette demande de modification.
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Récupère le planning associé à cette demande de modification.
     */
    public function planning()
    {
        return $this->belongsTo(Planning::class);
    }

    /**
     * Récupère le nouveau lieu associé à cette demande de modification.
     */
    public function nouveauLieu()
    {
        return $this->belongsTo(Lieu::class, 'nouveau_lieu_id');
    }
}
