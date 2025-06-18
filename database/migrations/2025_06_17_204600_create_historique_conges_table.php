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
        Schema::create('historique_conges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type_modification');
            $table->decimal('ancien_solde_conges', 5, 1);
            $table->decimal('nouveau_solde_conges', 5, 1);
            $table->decimal('ancien_solde_rtt', 5, 1);
            $table->decimal('nouveau_solde_rtt', 5, 1);
            $table->decimal('ancien_solde_conges_exceptionnels', 5, 1);
            $table->decimal('nouveau_solde_conges_exceptionnels', 5, 1);
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_conges');
    }
};
