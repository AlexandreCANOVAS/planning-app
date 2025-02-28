<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taux_heures_sup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societe_id')->constrained('societes')->onDelete('cascade');
            $table->string('nom');
            $table->integer('seuil_debut');
            $table->integer('seuil_fin')->nullable();
            $table->decimal('taux', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taux_heures_sup');
    }
};
