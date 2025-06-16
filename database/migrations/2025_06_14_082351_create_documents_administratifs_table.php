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
        Schema::create('documents_administratifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('type')->nullable(); // Type de document (CNI, passeport, permis, etc.)
            $table->string('numero')->nullable(); // Numéro du document
            $table->date('date_emission')->nullable(); // Date d'émission
            $table->date('date_expiration')->nullable(); // Date d'expiration
            $table->string('fichier')->nullable(); // Chemin vers le fichier stocké
            $table->boolean('fourni')->default(false); // Si le document a été fourni
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_administratifs');
    }
};
