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
        Schema::create('conge_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Utilisateur qui a modifié le statut');
            $table->enum('ancien_statut', ['en_attente', 'accepte', 'refuse']);
            $table->enum('nouveau_statut', ['en_attente', 'accepte', 'refuse']);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conge_histories');
    }
};
