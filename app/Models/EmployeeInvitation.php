<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'societe_id',
        'email',
        'token',
        'nom',
        'prenom',
        'poste',
        'expires_at',
    ];
}
