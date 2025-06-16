<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materiels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Type de matériel (ordinateur, téléphone, véhicule, etc.)
            $table->string('marque')->nullable(); // Marque du matériel
            $table->string('modele')->nullable(); // Modèle du matériel
            $table->string('numero_serie')->nullable(); // Numéro de série
            $table->string('identifiant')->nullable(); // Identifiant interne
            $table->date('date_attribution')->nullable(); // Date d'attribution
            $table->date('date_retour')->nullable(); // Date de retour prévue
            $table->text('description')->nullable(); // Description détaillée
            $table->text('etat')->nullable(); // État du matériel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiels');
    }
};
