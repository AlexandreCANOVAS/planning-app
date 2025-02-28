<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lieu extends Model
{
    use HasFactory;

    /**
     * Spécifier le nom de la table explicitement
     */
    protected $table = 'lieux';

    protected $fillable = [
        'nom',
        'adresse',
        'ville',
        'code_postal',
        'societe_id',
        'description',
        'couleur'
    ];

    /**
     * Relation avec la société
     */
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    /**
     * Relation avec les plannings
     */
    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Obtenir l'adresse complète
     */
    public function getAdresseCompleteAttribute(): string
    {
        return "{$this->adresse}, {$this->code_postal} {$this->ville}";
    }

    /**
     * Obtenir le nombre d'employés actuellement affectés à ce lieu
     */
    public function getEmployesCountAttribute(): int
    {
        return $this->plannings()
            ->where('date', now()->format('Y-m-d'))
            ->distinct('employe_id')
            ->count('employe_id');
    }

    /**
     * Scope pour les lieux d'une société spécifique
     */
    public function scopeDeSociete($query, $societeId)
    {
        return $query->where('societe_id', $societeId);
    }

    /**
     * Obtenir la couleur par défaut si non définie
     */
    public function getCouleurAttribute($value)
    {
        return $value ?? '#3498db';
    }
}