<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LieuTravail extends Model
{
    use HasFactory;

    protected $table = 'lieux_travail';

    protected $fillable = [
        'nom',
        'adresse',
        'couleur',
        'societe_id'
    ];

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class, 'lieu_id');
    }
}
