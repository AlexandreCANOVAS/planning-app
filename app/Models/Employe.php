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
    
    /**
     * Détermine le statut actuel de l'employé (disponible, en congé, etc.)
     */
    public function getStatutActuelAttribute()
    {
        // Vérifier si l'employé est en congé aujourd'hui
        $congeAujourdhui = $this->conges()
            ->where('statut', 'accepte')
            ->where('date_debut', '<=', now()->format('Y-m-d'))
            ->where('date_fin', '>=', now()->format('Y-m-d'))
            ->first();
            
        if ($congeAujourdhui) {
            return 'en_conge';
        }
        
        // Vérifier si l'employé a un planning aujourd'hui
        $planningAujourdhui = $this->plannings()
            ->whereDate('date', now()->format('Y-m-d'))
            ->first();
            
        if ($planningAujourdhui) {
            return 'en_service';
        }
        
        return 'disponible';
    }
    
    /**
     * Calcule la charge de travail de l'employé (pourcentage des heures travaillées par rapport à une semaine standard)
     */
    public function getChargeDeTravailAttribute()
    {
        // Calculer le nombre d'heures travaillées cette semaine
        $debutSemaine = now()->startOfWeek()->format('Y-m-d');
        $finSemaine = now()->endOfWeek()->format('Y-m-d');
        
        $planningsSemaine = $this->plannings()
            ->whereBetween('date', [$debutSemaine, $finSemaine])
            ->get();
            
        $heuresTravaillees = 0;
        
        foreach ($planningsSemaine as $planning) {
            // Calculer les heures entre heure_debut et heure_fin
            if ($planning->heure_debut && $planning->heure_fin) {
                // Utiliser parse qui est plus flexible avec les formats d'heure
                try {
                    $debut = \Carbon\Carbon::parse($planning->heure_debut);
                    $fin = \Carbon\Carbon::parse($planning->heure_fin);
                    
                    // Utiliser diffInHours avec le paramètre absolu à true pour avoir une valeur positive
                    $heuresTravaillees += $debut->diffInHours($fin, false);
                } catch (\Exception $e) {
                    // En cas d'erreur de parsing, on ignore cette entrée
                    continue;
                }
            }
        }
        
        // Une semaine standard est de 35 heures
        $heuresStandard = 35;
        
        return min(100, round(($heuresTravaillees / $heuresStandard) * 100));
    }
    
    /**
     * Calcule la progression des formations de l'employé
     */
    public function getProgressionFormationsAttribute()
    {
        // Si l'employé n'a pas de formations, retourner 0
        if ($this->formations->isEmpty()) {
            return 0;
        }
        
        $formationsAJour = 0;
        $totalFormations = $this->formations->count();
        
        foreach ($this->formations as $formation) {
            // Vérifier si la formation est à jour (pas de date de recyclage ou date de recyclage future)
            $dateRecyclage = $formation->pivot->date_recyclage;
            
            if (!$dateRecyclage || \Carbon\Carbon::parse($dateRecyclage)->isFuture()) {
                $formationsAJour++;
            }
        }
        
        return round(($formationsAJour / $totalFormations) * 100);
    }
}