<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societe_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->decimal('taux_horaire', 8, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tarifs');
    }
};
