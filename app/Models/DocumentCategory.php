<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentCategory extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'societe_id',
        'created_by'
    ];
    
    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
    
    /**
     * Obtenir les documents associés à cette catégorie.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }
    
    /**
     * Obtenir la société associée à cette catégorie (si applicable).
     */
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
    
    /**
     * Obtenir l'utilisateur qui a créé cette catégorie.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
