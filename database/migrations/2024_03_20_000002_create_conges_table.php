<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('duree', 4, 1); // Nombre de jours avec décimale
            $table->string('motif');
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conges');
    }
}; 