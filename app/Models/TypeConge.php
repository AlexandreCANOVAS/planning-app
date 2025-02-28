<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Conge;

class TypeConge extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'couleur'
    ];

    /**
     * Get the conges for this type
     */
    public function conges()
    {
        return $this->hasMany(Conge::class);
    }
}
