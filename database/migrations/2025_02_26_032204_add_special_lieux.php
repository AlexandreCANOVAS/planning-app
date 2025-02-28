<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Societe;
use App\Models\LieuTravail;

return new class extends Migration
{
    public function up()
    {
        $societes = Societe::all();

        foreach ($societes as $societe) {
            // Créer le lieu RH s'il n'existe pas
            if (!LieuTravail::where('societe_id', $societe->id)->where('nom', 'RH')->exists()) {
                LieuTravail::create([
                    'societe_id' => $societe->id,
                    'nom' => 'RH',
                    'adresse' => 'Repos hebdomadaire',
                    'couleur' => '#808080' // Gris
                ]);
            }

            // Créer le lieu CP s'il n'existe pas
            if (!LieuTravail::where('societe_id', $societe->id)->where('nom', 'CP')->exists()) {
                LieuTravail::create([
                    'societe_id' => $societe->id,
                    'nom' => 'CP',
                    'adresse' => 'Congés payés',
                    'couleur' => '#32CD32' // Vert lime
                ]);
            }
        }
    }

    public function down()
    {
        // Supprimer tous les lieux RH et CP
        LieuTravail::where('nom', 'RH')->orWhere('nom', 'CP')->delete();
    }
};
