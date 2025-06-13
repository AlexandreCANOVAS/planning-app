<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CongeHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'conge_id',
        'user_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire'
    ];
    
    /**
     * Relation avec le congé
     */
    public function conge()
    {
        return $this->belongsTo(Conge::class);
    }
    
    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
