<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employe extends Model
{
    use HasFactory;

    protected $appends = ['formation_count'];

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'societe_id',
        'user_id'
    ];

    protected $casts = [];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
     * Relation avec les congés
     */
    public function conges()
    {
        return $this->hasMany(Conge::class);
    }

    /**
     * Relation avec les formations
     */
    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class, 'employe_formation')
            ->withPivot(['date_obtention', 'date_recyclage', 'commentaire'])
            ->withTimestamps();
    }

    /**
     * Get the number of formations for the employee
     */
    public function getFormationCountAttribute()
    {
        return optional($this->formations)->count() ?? 0;
    }
    
    /**
     * Calcule le solde de congés payés restants
     */
    public function getSoldeCongesAttribute()
    {
        // Par défaut, on attribue 25 jours de congés payés par an
        $soldeInitial = 25.0;
        
        // Récupérer les congés acceptés de l'année en cours
        $anneeEnCours = now()->year;
        $congesAcceptes = $this->conges()
            ->where('statut', 'accepte')
            ->whereYear('date_debut', $anneeEnCours)
            ->get();
        
        // Soustraire la durée des congés acceptés
        $totalCongesPris = $congesAcceptes->sum('duree');
        
        return $soldeInitial - $totalCongesPris;
    }
    
    /**
     * Calcule le nombre de jours de congés pris par type de statut
     */
    public function getCongeStats()
    {
        $anneeEnCours = now()->year;
        
        $stats = [
            'accepte' => 0,
            'en_attente' => 0,
            'refuse' => 0
        ];
        
        foreach ($this->conges()->whereYear('date_debut', $anneeEnCours)->get() as $conge) {
            $stats[$conge->statut] += $conge->duree;
        }
        
        return $stats;
    }
} 