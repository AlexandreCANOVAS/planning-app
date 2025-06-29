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
        Schema::table('employes', function (Blueprint $table) {
            // Informations personnelles
            $table->date('date_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->string('numero_securite_sociale')->nullable();
            $table->string('situation_familiale')->nullable();
            $table->integer('nombre_enfants')->nullable();
            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_telephone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn([
                'date_naissance',
                'adresse',
                'numero_securite_sociale',
                'situation_familiale',
                'nombre_enfants',
                'contact_urgence_nom',
                'contact_urgence_telephone'
            ]);
        });
    }
};
