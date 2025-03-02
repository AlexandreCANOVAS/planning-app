<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\Lieu;
use App\Models\Employe;
use App\Models\Societe;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'lieu_id',
        'societe_id',
        'date',
        'heure_debut',
        'heure_fin',
        'heures_travaillees',
        'periode',
        'heures_majorees',
        'heures_complementaires'
    ];

    protected $casts = [
        'date' => 'datetime',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'heures_travaillees' => 'float',
        'heures_majorees' => 'float',
        'heures_complementaires' => 'float'
    ];

    /**
     * Relation avec l'employé
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Relation avec le lieu
     */
    public function lieu(): BelongsTo
    {
        return $this->belongsTo(Lieu::class);
    }

    /**
     * Relation avec la société
     */
    public function societe(): BelongsTo
    {
        return $this->belongsTo(Societe::class);
    }

    /**
     * Accesseur pour formater les heures travaillées
     */
    public function getHeuresTravailleesFormateesAttribute()
    {
        return number_format($this->heures_travaillees, 2) . 'h';
    }

    /**
     * Accesseur pour formater la date en français
     */
    public function getDateFormateeFrAttribute()
    {
        return $this->date->locale('fr')->isoFormat('dddd D MMMM YYYY');
    }

    /**
     * Accesseur pour formater les horaires
     */
    public function getHorairesFormatesAttribute()
    {
        return $this->heure_debut->format('H:i') . ' - ' . $this->heure_fin->format('H:i');
    }

    /**
     * Accesseur pour formater l'heure de début
     */
    public function getHeureDebutFormatteeAttribute()
    {
        return $this->heure_debut->format('H:i');
    }

    /**
     * Accesseur pour formater l'heure de fin
     */
    public function getHeureFinFormatteeAttribute()
    {
        return $this->heure_fin->format('H:i');
    }

    /**
     * Accesseur pour formater la date
     */
    public function getDateFormatteeAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Accesseur pour obtenir le jour de la semaine
     */
    public function getJourSemaineAttribute()
    {
        return $this->date->locale('fr_FR')->isoFormat('dddd');
    }

    /**
     * Accesseur pour obtenir le nombre d'heures travaillées
     */
    public function getHeuresTravailleesFormatteeAttribute()
    {
        return number_format($this->heures_travaillees, 2) . ' h';
    }

    /**
     * Mutateur pour calculer automatiquement les heures travaillées
     */
    protected static function booted()
    {
        static::saving(function ($planning) {
            // Si c'est un RH ou CP, mettre les heures travaillées à 0
            if ($planning->lieu && in_array($planning->lieu->nom, ['RH', 'CP'])) {
                $planning->heures_travaillees = 0;
                return;
            }

            // Pour les autres lieux, calculer les heures travaillées
            if ($planning->heure_debut && $planning->heure_fin) {
                $debut = $planning->heure_debut;
                $fin = $planning->heure_fin;
                
                // Si l'heure de fin est avant l'heure de début, on ajoute 24h
                if ($fin < $debut) {
                    $fin->addDay();
                }
                
                // Calculer les heures travaillées
                $planning->heures_travaillees = abs($fin->floatDiffInHours($debut));
            }
        });
    }
}