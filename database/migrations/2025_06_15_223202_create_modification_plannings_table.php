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
        Schema::create('modification_plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->foreignId('planning_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type_modification', ['horaires', 'lieu', 'absence', 'autre']);
            $table->datetime('date_demande');
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');
            $table->text('motif');
            $table->text('details')->nullable();
            $table->date('nouvelle_date')->nullable();
            $table->foreignId('nouveau_lieu_id')->nullable()->constrained('lieux')->onDelete('set null');
            $table->time('nouvelle_heure_debut')->nullable();
            $table->time('nouvelle_heure_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modification_plannings');
    }
};
