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
        'telephone',
        'horaires',
        'contact_principal',
        'latitude',
        'longitude',
        'description',
        'couleur',
        'societe_id',
        'is_special'
    ];

    protected $casts = [
        'is_special' => 'boolean'
    ];

    protected $appends = ['adresse_complete', 'employes_count'];

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
        return $this->hasMany(Planning::class, 'lieu_id');
    }

    /**
     * Obtenir l'adresse complète
     */
    public function getAdresseCompleteAttribute(): string
    {
        if ($this->is_special) {
            return $this->nom;
        }
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
        return $query->where(function($q) use ($societeId) {
            $q->where('societe_id', $societeId)
              ->orWhereNull('societe_id')
              ->orWhere('is_special', true);
        });
    }

    /**
     * Obtenir la couleur par défaut si non définie
     */
    public function getCouleurAttribute($value)
    {
        return $value ?? '#3498db';
    }
}