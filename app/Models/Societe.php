<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Societe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'siret',
        'forme_juridique',
        'adresse',
        'telephone',
        'user_id'
    ];

    /**
     * Relation avec l'employeur (user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les employés
     */
    public function employes()
    {
        return $this->hasMany(User::class)->where('role', 'employe');
    }

    /**
     * Relation avec les lieux
     */
    public function lieux()
    {
        return $this->hasMany(Lieu::class);
    }

    /**
     * Relation avec les plannings via les lieux
     */
    public function plannings()
    {
        return $this->hasManyThrough(Planning::class, Lieu::class);
    }

    public function employeur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec les congés via les employés
     */
    public function conges()
    {
        return $this->hasManyThrough(
            Conge::class,
            Employe::class,
            'societe_id', // Clé étrangère sur la table employes
            'employe_id', // Clé étrangère sur la table conges
            'id', // Clé locale sur la table societes
            'id' // Clé locale sur la table employes
        );
    }

    /**
     * Relation avec les lieux de travail
     */
    public function lieuxTravail()
    {
        return $this->hasMany(LieuTravail::class);
    }
}