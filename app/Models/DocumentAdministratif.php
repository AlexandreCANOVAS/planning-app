<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAdministratif extends Model
{
    use HasFactory;
    
    protected $table = 'documents_administratifs';
    
    protected $fillable = [
        'employe_id',
        'nom',
        'type',
        'numero',
        'date_emission',
        'date_expiration',
        'fichier',
        'fourni',
        'notes',
    ];
    
    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'fourni' => 'boolean',
    ];
    
    /**
     * Get the employee that owns the document.
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
