<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\Lieu;
use App\Models\LieuTravail;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'societe_id',
        'lieu_id',
        'date',
        'heure_debut',
        'heure_fin',
        'heures_travaillees',
        'heures_composees',
        'description',
        'periode',
        'heures_matin',
        'heures_aprem'
    ];

    protected $casts = [
        'date' => 'datetime',
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
        'heures_travaillees' => 'float',
        'heures_composees' => 'float',
        'heures_matin' => 'json',
        'heures_aprem' => 'json'
    ];

    /**
     * Relation avec l'employé
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Relation avec le lieu
     */
    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }

    /**
     * Relation avec la société
     */
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    /**
     * Relation avec le lieu de travail
     */
    public function lieuTravail()
    {
        return $this->belongsTo(LieuTravail::class, 'lieu_id');
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
        return Carbon::parse($this->heure_debut)->format('H:i');
    }

    /**
     * Accesseur pour formater l'heure de fin
     */
    public function getHeureFinFormatteeAttribute()
    {
        return Carbon::parse($this->heure_fin)->format('H:i');
    }

    /**
     * Accesseur pour formater la date
     */
    public function getDateFormatteeAttribute()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }

    /**
     * Accesseur pour obtenir le jour de la semaine
     */
    public function getJourSemaineAttribute()
    {
        return Carbon::parse($this->date)->locale('fr_FR')->isoFormat('dddd');
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
            if ($planning->heure_debut && $planning->heure_fin) {
                $debut = $planning->heure_debut->copy();
                $fin = $planning->heure_fin->copy();
                
                // Si l'heure de fin est avant l'heure de début, on ajoute 24h
                if ($fin < $debut) {
                    $fin->addDay();
                }
                
                $planning->heures_travaillees = $fin->diffInMinutes($debut) / 60;
            }
        });
    }
}