<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TauxMajoration extends Model
{
    protected $table = 'taux_majorations';

    protected $fillable = [
        'societe_id',
        'type',
        'base',
        'taux'
    ];

    const TYPES = [
        'jour_ferie' => 'Majoration heures de jour férié',
        'dimanche' => 'Majoration heures de dimanche',
        'nuit' => 'Majoration heures de nuit',
        'habillage' => 'Prime de temps d\'habillage et de déshabillage',
        'nuit_20' => 'Majoration nuit 20%',
        'ferie_nuit' => 'Majoration férié nuit 110%',
        'panier_repas' => 'Indemnité de panier repas',
        'entretien_tenue' => 'Indemnité entretien des tenues'
    ];

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    public function getTaux($type)
    {
        switch ($type) {
            case 'jour_ferie':
                return $this->jour_ferie ?? 12.217;
            case 'dimanche':
                return $this->dimanche ?? 12.217;
            case 'nuit':
                return $this->nuit ?? 12.217;
            case 'heures_sup_25':
                return $this->heures_sup_25 ?? 12.217;
            case 'heures_sup_50':
                return $this->heures_sup_50 ?? 12.217;
            default:
                return 0;
        }
    }
}
