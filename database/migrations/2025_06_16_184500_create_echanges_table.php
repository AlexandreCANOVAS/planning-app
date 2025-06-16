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
        Schema::create('echanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->foreignId('target_employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date');
            $table->date('target_date');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->foreignId('societe_id')->constrained()->onDelete('cascade');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echanges');
    }
};
