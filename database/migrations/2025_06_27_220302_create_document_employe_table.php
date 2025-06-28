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
        Schema::create('document_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->timestamp('consulte_le')->nullable(); // Date de consultation du document
            $table->boolean('confirme_lecture')->default(false); // Si le document requiert une confirmation de lecture
            $table->timestamp('confirme_le')->nullable(); // Date de confirmation de lecture
            $table->timestamps();
            
            // Chaque document ne peut être associé qu'une seule fois à un employé
            $table->unique(['document_id', 'employe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_employe');
    }
};
