<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoriqueConge extends Model
{
    use HasFactory;

    protected $table = 'historique_conges';

    protected $fillable = [
        'employe_id',
        'user_id',
        'type_modification',
        'ancien_solde_conges',
        'nouveau_solde_conges',
        'ancien_solde_rtt',
        'nouveau_solde_rtt',
        'ancien_solde_conges_exceptionnels',
        'nouveau_solde_conges_exceptionnels',
        'commentaire',
    ];

    protected $casts = [
        'ancien_solde_conges' => 'decimal:1',
        'nouveau_solde_conges' => 'decimal:1',
        'ancien_solde_rtt' => 'decimal:1',
        'nouveau_solde_rtt' => 'decimal:1',
        'ancien_solde_conges_exceptionnels' => 'decimal:1',
        'nouveau_solde_conges_exceptionnels' => 'decimal:1',
    ];

    /**
     * Relation avec l'employé
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Relation avec l'utilisateur qui a effectué la modification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
