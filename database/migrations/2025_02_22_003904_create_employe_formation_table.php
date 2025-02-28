<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employe_formation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->foreignId('formation_id')->constrained()->onDelete('cascade');
            $table->date('date_obtention');
            $table->date('date_recyclage')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            // Un employé ne peut avoir qu'une seule fois la même formation
            $table->unique(['employe_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employe_formation');
    }
};
