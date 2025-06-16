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
        Schema::create('echange_jours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demandeur_id')->constrained('employes')->onDelete('cascade');
            $table->foreignId('receveur_id')->constrained('employes')->onDelete('cascade');
            $table->date('jour_demandeur'); // Jour que le demandeur souhaite échanger
            $table->date('jour_receveur'); // Jour du receveur que le demandeur souhaite
            $table->text('motif')->nullable();
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->text('commentaire_reponse')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echange_jours');
    }
};
