<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Créer une table temporaire pour stocker les données fusionnées
        Schema::create('lieux_temp', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('adresse');
            $table->string('ville')->default('À définir');
            $table->string('code_postal')->default('00000');
            $table->text('description')->nullable();
            $table->string('couleur')->default('#3498db');
            $table->foreignId('societe_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Copier les données de la table lieux
        $lieux = DB::table('lieux')->get();
        foreach ($lieux as $lieu) {
            DB::table('lieux_temp')->insert([
                'id' => $lieu->id,
                'nom' => $lieu->nom,
                'adresse' => $lieu->adresse,
                'ville' => $lieu->ville,
                'code_postal' => $lieu->code_postal,
                'description' => $lieu->description,
                'couleur' => $lieu->couleur ?? '#3498db',
                'societe_id' => $lieu->societe_id,
                'created_at' => $lieu->created_at,
                'updated_at' => $lieu->updated_at
            ]);
        }

        // 3. Copier les données de la table lieux_travail qui n'existent pas déjà
        $lieuxTravail = DB::table('lieux_travail')->get();
        foreach ($lieuxTravail as $lieu) {
            $exists = DB::table('lieux_temp')
                ->where('nom', $lieu->nom)
                ->where('societe_id', $lieu->societe_id)
                ->exists();

            if (!$exists) {
                DB::table('lieux_temp')->insert([
                    'nom' => $lieu->nom,
                    'adresse' => $lieu->adresse,
                    'ville' => $lieu->ville ?? 'À définir',
                    'code_postal' => $lieu->code_postal ?? '00000',
                    'societe_id' => $lieu->societe_id,
                    'couleur' => $lieu->couleur ?? '#3498db',
                    'created_at' => $lieu->created_at,
                    'updated_at' => $lieu->updated_at
                ]);
            }
        }

        // 4. Mettre à jour les références dans la table plannings
        Schema::table('plannings', function (Blueprint $table) {
            $table->dropForeign(['lieu_id']);
        });

        // 5. Supprimer les anciennes tables
        Schema::dropIfExists('lieux');
        Schema::dropIfExists('lieux_travail');

        // 6. Renommer la table temporaire
        Schema::rename('lieux_temp', 'lieux');

        // 7. Recréer la contrainte de clé étrangère dans plannings
        Schema::table('plannings', function (Blueprint $table) {
            $table->foreign('lieu_id')->references('id')->on('lieux')->onDelete('cascade');
        });

        // 8. Notifier les utilisateurs qu'ils doivent mettre à jour les informations manquantes
        DB::table('lieux')
            ->where('ville', 'À définir')
            ->orWhere('code_postal', '00000')
            ->update(['description' => 'ATTENTION : Veuillez mettre à jour l\'adresse complète de ce lieu.']);
    }

    public function down(): void
    {
        // Cette migration est irréversible car elle fusionne des données
        // Nous ne pouvons pas savoir avec certitude quelles données venaient de quelle table
    }
};
