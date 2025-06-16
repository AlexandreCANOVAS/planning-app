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
        Schema::create('badges_acces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('numero_badge'); // Numéro du badge
            $table->string('type')->nullable(); // Type de badge
            $table->text('zones_acces')->nullable(); // Zones accessibles avec ce badge
            $table->date('date_emission')->nullable(); // Date d'émission du badge
            $table->date('date_expiration')->nullable(); // Date d'expiration du badge
            $table->boolean('actif')->default(true); // Si le badge est actif
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges_acces');
    }
};
