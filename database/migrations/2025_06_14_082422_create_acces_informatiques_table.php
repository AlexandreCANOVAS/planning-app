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
        Schema::create('acces_informatiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('systeme'); // Nom du système (ERP, CRM, email, etc.)
            $table->string('identifiant')->nullable(); // Identifiant de connexion
            $table->string('niveau_acces')->nullable(); // Niveau d'accès (admin, utilisateur, etc.)
            $table->text('permissions')->nullable(); // Détail des permissions
            $table->date('date_creation')->nullable(); // Date de création du compte
            $table->date('date_expiration')->nullable(); // Date d'expiration du compte
            $table->boolean('actif')->default(true); // Si le compte est actif
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acces_informatiques');
    }
};
