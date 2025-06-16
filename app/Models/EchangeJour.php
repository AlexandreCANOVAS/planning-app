<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EchangeJour extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'echange_jours';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'demandeur_id',
        'receveur_id',
        'jour_demandeur',
        'jour_receveur',
        'motif',
        'statut',
        'commentaire_reponse',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jour_demandeur' => 'date',
        'jour_receveur' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtenir l'employé qui demande l'échange.
     */
    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'demandeur_id');
    }

    /**
     * Obtenir l'employé qui reçoit la demande d'échange.
     */
    public function receveur(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'receveur_id');
    }

    /**
     * Vérifie si l'échange est en attente.
     *
     * @return bool
     */
    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifie si l'échange est accepté.
     *
     * @return bool
     */
    public function estAccepte(): bool
    {
        return $this->statut === 'accepte';
    }

    /**
     * Vérifie si l'échange est refusé.
     *
     * @return bool
     */
    public function estRefuse(): bool
    {
        return $this->statut === 'refuse';
    }
}
