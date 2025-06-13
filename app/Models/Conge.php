<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conge extends Model
{
    use HasFactory;
    
    /**
     * Les statuts possibles pour une demande de congé
     */
    const STATUTS = [
        'en_attente' => 'En attente',
        'accepte' => 'Accepté',
        'refuse' => 'Refusé'
    ];

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

    // Les statuts sont définis comme constante plus haut dans le fichier

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
    
    /**
     * Relation avec l'historique des modifications de statut
     */
    public function historique()
    {
        return $this->hasMany(CongeHistory::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Vérifie s'il y a des chevauchements avec d'autres congés
     */
    public function chevauchements()
    {
        if (!$this->employe) {
            return collect();
        }
        
        return Conge::where('employe_id', '!=', $this->employe_id)
            ->where(function($query) {
                $query->whereBetween('date_debut', [$this->date_debut, $this->date_fin])
                    ->orWhereBetween('date_fin', [$this->date_debut, $this->date_fin])
                    ->orWhere(function($q) {
                        $q->where('date_debut', '<=', $this->date_debut)
                          ->where('date_fin', '>=', $this->date_fin);
                    });
            })
            ->where('statut', 'accepte')
            ->with('employe')
            ->get();
    }
} 