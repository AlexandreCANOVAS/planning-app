<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('type_conges', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('couleur')->default('#4A5568'); // Couleur par défaut gris
            $table->timestamps();
        });

        // Ajout de la colonne type_conge_id à la table conges
        Schema::table('conges', function (Blueprint $table) {
            $table->foreignId('type_conge_id')
                  ->nullable()
                  ->constrained('type_conges')
                  ->onDelete('set null');
        });

        // Insérer quelques types de congés par défaut
        DB::table('type_conges')->insert([
            [
                'nom' => 'Congés payés',
                'description' => 'Congés annuels payés',
                'couleur' => '#48BB78', // vert
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'RTT',
                'description' => 'Réduction du Temps de Travail',
                'couleur' => '#4299E1', // bleu
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Maladie',
                'description' => 'Congé maladie',
                'couleur' => '#F56565', // rouge
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conges', function (Blueprint $table) {
            $table->dropConstrainedForeignId('type_conge_id');
        });
        Schema::dropIfExists('type_conges');
    }
};
