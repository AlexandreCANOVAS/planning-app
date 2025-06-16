<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeAcces extends Model
{
    use HasFactory;
    
    protected $table = 'badges_acces';
    
    protected $fillable = [
        'employe_id',
        'numero_badge',
        'type',
        'zones_acces',
        'date_emission',
        'date_expiration',
        'actif',
        'notes',
    ];
    
    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'actif' => 'boolean',
    ];
    
    /**
     * Get the employee that owns the badge.
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
