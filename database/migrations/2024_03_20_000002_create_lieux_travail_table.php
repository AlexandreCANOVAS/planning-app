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
        Schema::create('lieux_travail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('societe_id')->nullable()->constrained('societes')->cascadeOnDelete();
            $table->string('nom');
            $table->string('adresse');
            $table->string('couleur')->default('#000000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lieux_travail');
    }
};
