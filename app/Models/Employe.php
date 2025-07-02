<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\EmployeFormation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Employe extends Model
{
    use HasFactory;

    protected $appends = ['formation_count'];

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'date_embauche',
        'photo_profil',
        'date_naissance',
        'numero_securite_sociale',
        'situation_familiale',
        'nombre_enfants',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'poste',
        'type_contrat',
        'date_debut_contrat',
        'date_fin_contrat',
        'temps_travail',
        'pourcentage_travail',
        'solde_conges',
        'solde_rtt',
        'solde_conges_exceptionnels',
        'societe_id',
        'user_id'
    ];

    protected $casts = [
        'solde_conges' => 'decimal:1',
        'solde_rtt' => 'decimal:1',
        'solde_conges_exceptionnels' => 'decimal:1',
        'date_embauche' => 'date',
        'date_naissance' => 'date',
        'date_debut_contrat' => 'date',
        'date_fin_contrat' => 'date',
    ];

    /**
     * Chiffre le numéro de sécurité sociale avant de l'enregistrer.
     */
    public function setNumeroSecuriteSocialeAttribute($value)
    {
        $this->attributes['numero_securite_sociale'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Déchiffre le numéro de sécurité sociale lors de sa lecture.
     */
    public function getNumeroSecuriteSocialeAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Gère le cas où la donnée n'est pas chiffrée (ancienne donnée)
            return $value; 
        }
    }
    
    /**
     * Get the user's display name (always returns prenom instead of nom)
     */
    public function getNameAttribute()
    {
        return $this->prenom;
    }

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
     * Relation avec les fiches de paie
     */
    public function fichesPaie()
    {
        return $this->hasMany(FichePaie::class);
    }

    /**
     * Relation avec les formations
     */
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'employe_formation')
            ->withPivot(['date_obtention', 'date_recyclage', 'last_recyclage', 'commentaire'])
            ->withTimestamps();
    }
    
    /**
     * Relation avec les documents administratifs
     */
    public function documentsAdministratifs()
    {
        return $this->hasMany(DocumentAdministratif::class);
    }
    
    /**
     * Relation avec l'historique des modifications de solde de congés
     */
    public function historiqueConges()
    {
        return $this->hasMany(HistoriqueConge::class);
    }
    
    /**
     * Relation avec les matériels attribués
     */
    public function materiels()
    {
        return $this->hasMany(Materiel::class);
    }
    
    /**
     * Relation avec les badges d'accès
     */
    public function badgesAcces()
    {
        return $this->hasMany(BadgeAcces::class);
    }
    
    /**
     * Relation avec les accès informatiques
     */
    public function accesInformatiques()
    {
        return $this->hasMany(AccesInformatique::class);
    }
    
    /**
     * Relation avec les documents
     */
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_employe')
                    ->withPivot(['consulte_le', 'confirme_lecture', 'confirme_le'])
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
     * Calcule le solde de congés payés restants (méthode renommée pour éviter les conflits avec l'attribut)
     */
    public function calculerSoldeConges()
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