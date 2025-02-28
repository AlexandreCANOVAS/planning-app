<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conge extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'date_debut',
        'date_fin',
        'duree',
        'motif',
        'statut',
        'commentaire',
        'type_conge_id'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'duree' => 'decimal:1'
    ];

    /**
     * Les statuts possibles pour une demande de congé
     */
    const STATUTS = [
        'en_attente' => 'En attente',
        'accepte' => 'Accepté',
        'refuse' => 'Refusé'
    ];

    /**
     * Relation avec l'employé
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Relation avec le type de congé
     */
    public function type()
    {
        return $this->belongsTo(TypeConge::class, 'type_conge_id');
    }

    /**
     * Vérifie si le congé est en attente
     */
    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifie si le congé est accepté
     */
    public function isAccepte(): bool
    {
        return $this->statut === 'accepte';
    }

    /**
     * Vérifie si le congé est refusé
     */
    public function isRefuse(): bool
    {
        return $this->statut === 'refuse';
    }
} 