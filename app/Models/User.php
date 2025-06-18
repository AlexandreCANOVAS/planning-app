<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'societe_id',
        'phone',
        'password_changed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed' => 'boolean',
        ];
    }

    /**
     * Relation avec la société (pour les employeurs)
     */
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    /**
     * Get the employe associated with the user.
     */
    public function employe(): HasOne
    {
        return $this->hasOne(Employe::class);
    }

    /**
     * Vérifie si l'utilisateur est un employé
     */
    public function isEmploye()
    {
        return $this->role === 'employe';
    }

    /**
     * Vérifie si l'utilisateur est un employeur
     */
    public function isEmployeur()
    {
        return $this->role === 'employeur';
    }
    
    /**
     * Accesseur pour afficher le prénom au lieu du nom
     */
    public function getNameAttribute($value)
    {
        // Si c'est un employé et qu'il a un employe associé avec un prénom
        if ($this->isEmploye() && $this->employe && $this->employe->prenom) {
            return $this->employe->prenom;
        }
        
        // Sinon retourner le nom par défaut
        return $value;
    }

    /**
     * Accès aux congés via la relation employe
     */
    public function conges()
    {
        return $this->hasOneThrough(
            'App\Models\Conge',
            'App\Models\Employe',
            'user_id', // Clé étrangère sur employes
            'employe_id', // Clé étrangère sur conges
            'id', // Clé locale sur users
            'id' // Clé locale sur employes
        );
    }
}
