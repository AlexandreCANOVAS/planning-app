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
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->foreignId('societe_id')->constrained()->onDelete('cascade');
            $table->foreignId('lieu_id')->nullable()->constrained('lieux_travail')->onDelete('cascade');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->decimal('heures_travaillees', 5, 2);
            $table->string('type')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes fréquentes
            $table->index(['employe_id', 'date']);
            $table->index(['lieu_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};