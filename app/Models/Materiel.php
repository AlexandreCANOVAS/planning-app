<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materiel extends Model
{
    use HasFactory;
    
    protected $table = 'materiels';
    
    protected $fillable = [
        'employe_id',
        'type',
        'marque',
        'modele',
        'numero_serie',
        'identifiant',
        'date_attribution',
        'date_retour',
        'description',
        'etat',
    ];
    
    protected $casts = [
        'date_attribution' => 'date',
        'date_retour' => 'date',
    ];
    
    /**
     * Get the employee that owns the material.
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
