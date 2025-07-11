<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Formation extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'objectifs_pedagogiques',
        'prerequis',
        'duree_recommandee_heures',
        'organisme_formateur',
        'formateur_interne',
        'cout',
        'duree_validite_mois',
        'societe_id',
    ];

    protected $casts = [
        'duree_validite_mois' => 'integer',
        'duree_recommandee_heures' => 'integer',
        'formateur_interne' => 'boolean',
        'cout' => 'decimal:2'
    ];

    public function societe(): BelongsTo
    {
        return $this->belongsTo(Societe::class);
    }

    public function employes(): BelongsToMany
    {
        return $this->belongsToMany(Employe::class, 'employe_formation')
            ->withPivot(['date_obtention', 'date_recyclage', 'last_recyclage', 'commentaire'])
            ->withTimestamps()
            ->using(EmployeFormation::class);
    }
}
