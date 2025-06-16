<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccesInformatique extends Model
{
    use HasFactory;
    
    protected $table = 'acces_informatiques';
    
    protected $fillable = [
        'employe_id',
        'systeme',
        'identifiant',
        'niveau_acces',
        'permissions',
        'date_creation',
        'date_expiration',
        'actif',
        'notes',
    ];
    
    protected $casts = [
        'date_creation' => 'date',
        'date_expiration' => 'date',
        'actif' => 'boolean',
    ];
    
    /**
     * Get the employee that owns the IT access.
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
