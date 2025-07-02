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
        Schema::create('fiches_paie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('mois', 7); // Format: YYYY-MM
            $table->decimal('salaire_base', 10, 2);
            $table->decimal('heures_normales', 8, 2)->default(0);
            $table->decimal('heures_sup_25', 8, 2)->default(0);
            $table->decimal('heures_sup_50', 8, 2)->default(0);
            $table->decimal('heures_nuit', 8, 2)->default(0);
            $table->decimal('heures_dimanche', 8, 2)->default(0);
            $table->decimal('heures_jours_feries', 8, 2)->default(0);
            $table->decimal('montant_heures_normales', 10, 2)->default(0);
            $table->decimal('montant_heures_sup_25', 10, 2)->default(0);
            $table->decimal('montant_heures_sup_50', 10, 2)->default(0);
            $table->decimal('montant_heures_nuit', 10, 2)->default(0);
            $table->decimal('montant_heures_dimanche', 10, 2)->default(0);
            $table->decimal('montant_heures_jours_feries', 10, 2)->default(0);
            $table->decimal('prime_transport', 10, 2)->default(0);
            $table->decimal('prime_anciennete', 10, 2)->default(0);
            $table->decimal('prime_performance', 10, 2)->default(0);
            $table->decimal('autres_primes', 10, 2)->default(0);
            $table->decimal('indemnites_repas', 10, 2)->default(0);
            $table->decimal('salaire_brut', 10, 2);
            $table->decimal('cotisations_salariales', 10, 2);
            $table->decimal('cotisations_patronales', 10, 2);
            $table->decimal('impot_revenu', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2);
            $table->decimal('salaire_net_a_payer', 10, 2);
            $table->text('commentaires')->nullable();
            $table->string('statut')->default('brouillon'); // brouillon, validé, publié
            $table->dateTime('date_validation')->nullable();
            $table->dateTime('date_publication')->nullable();
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['employe_id', 'mois']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiches_paie');
    }
};
