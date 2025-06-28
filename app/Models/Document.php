<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;
    
    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'fichier_path',
        'type_fichier',
        'categorie',
        'visible_pour_tous',
        'societe_id',
        'uploaded_by',
        'date_expiration',
    ];
    
    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visible_pour_tous' => 'boolean',
        'date_expiration' => 'datetime',
    ];
    
    /**
     * Obtenir la société à laquelle appartient ce document.
     */
    public function societe(): BelongsTo
    {
        return $this->belongsTo(Societe::class);
    }
    
    /**
     * Obtenir l'utilisateur qui a téléversé ce document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    /**
     * Les employés qui ont accès à ce document.
     */
    public function employes(): BelongsToMany
    {
        return $this->belongsToMany(Employe::class, 'document_employe')
                    ->withPivot(['consulte_le', 'confirme_lecture', 'confirme_le'])
                    ->withTimestamps();
    }
    
    /**
     * Vérifie si un employé spécifique a accès à ce document.
     *
     * @param int $employeId
     * @return bool
     */
    public function isAccessibleBy(int $employeId): bool
    {
        if ($this->visible_pour_tous) {
            return true;
        }
        
        return $this->employes()->where('employe_id', $employeId)->exists();
    }
    
    /**
     * Marque un document comme consulté par un employé.
     *
     * @param int $employeId
     * @return void
     */
    public function markAsViewed(int $employeId): void
    {
        $this->employes()->updateExistingPivot($employeId, [
            'consulte_le' => now(),
        ]);
    }
    
    /**
     * Marque un document comme lu et confirmé par un employé.
     *
     * @param int $employeId
     * @return void
     */
    public function markAsConfirmed(int $employeId): void
    {
        $this->employes()->updateExistingPivot($employeId, [
            'confirme_lecture' => true,
            'confirme_le' => now(),
        ]);
    }
    
    /**
     * Obtient l'URL pour télécharger le document.
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return route('employe.documents.download', $this->id);
    }
    
    /**
     * Obtient le chemin complet du fichier dans le stockage.
     *
     * @return string
     */
    public function getFullPath(): string
    {
        return Storage::path($this->fichier_path);
    }
    
    /**
     * Vérifie si le document est expiré.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }
}
