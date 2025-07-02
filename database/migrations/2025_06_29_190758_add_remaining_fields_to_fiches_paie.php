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
        Schema::table('fiches_paie', function (Blueprint $table) {
            // Heures spéciales (uniquement les champs manquants)
            $table->decimal('heures_dimanche_ferie', 8, 2)->default(0);
            $table->decimal('montant_heures_dimanche_ferie', 10, 2)->default(0);
            
            // Détails des cotisations
            $table->decimal('cotisation_maladie', 10, 2)->default(0);
            $table->decimal('complementaire_sante', 10, 2)->default(0);
            $table->decimal('assurance_chomage', 10, 2)->default(0);
            $table->decimal('retraite_securite_sociale', 10, 2)->default(0);
            $table->decimal('retraite_complementaire', 10, 2)->default(0);
            $table->decimal('prevoyance', 10, 2)->default(0);
            $table->decimal('autres_contributions', 10, 2)->default(0);
            
            // CSG/CRDS
            $table->decimal('csg_deductible', 10, 2)->default(0);
            $table->decimal('csg_non_deductible', 10, 2)->default(0);
            $table->decimal('crds', 10, 2)->default(0);
            
            // Exonérations et allègements
            $table->decimal('exonerations_allegements', 10, 2)->default(0);
            
            // Avantages en nature
            $table->decimal('avantages_nature', 10, 2)->default(0);
            
            // Autres éléments (uniquement les champs manquants)
            $table->decimal('prime_panier', 10, 2)->default(0);
            $table->decimal('prime_habillage', 10, 2)->default(0);
            $table->decimal('acompte', 10, 2)->default(0);
            
            // Totaux
            $table->decimal('total_cotisations', 10, 2)->default(0);
            $table->decimal('montant_net_social', 10, 2)->default(0);
            $table->decimal('net_a_payer_avant_impot', 10, 2)->default(0);
            $table->decimal('impot_preleve_source', 10, 2)->default(0);
            $table->decimal('total_cotisations_patronales', 10, 2)->default(0);
            
            // Cumuls
            $table->decimal('cumul_brut', 10, 2)->default(0);
            $table->decimal('cumul_imposable', 10, 2)->default(0);
            $table->decimal('cumul_net', 10, 2)->default(0);
            $table->decimal('cumul_cotisations_patronales', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fiches_paie', function (Blueprint $table) {
            // Heures spéciales (uniquement les nouvelles)
            $table->dropColumn('heures_dimanche_ferie');
            $table->dropColumn('montant_heures_dimanche_ferie');
            
            // Détails des cotisations
            $table->dropColumn('cotisation_maladie');
            $table->dropColumn('complementaire_sante');
            $table->dropColumn('assurance_chomage');
            $table->dropColumn('retraite_securite_sociale');
            $table->dropColumn('retraite_complementaire');
            $table->dropColumn('prevoyance');
            $table->dropColumn('autres_contributions');
            
            // CSG/CRDS
            $table->dropColumn('csg_deductible');
            $table->dropColumn('csg_non_deductible');
            $table->dropColumn('crds');
            
            // Exonérations et allègements
            $table->dropColumn('exonerations_allegements');
            
            // Avantages en nature
            $table->dropColumn('avantages_nature');
            
            // Autres éléments (uniquement les nouveaux)
            $table->dropColumn('prime_panier');
            $table->dropColumn('prime_habillage');
            $table->dropColumn('acompte');
            
            // Totaux
            $table->dropColumn('total_cotisations');
            $table->dropColumn('montant_net_social');
            $table->dropColumn('net_a_payer_avant_impot');
            $table->dropColumn('impot_preleve_source');
            $table->dropColumn('total_cotisations_patronales');
            
            // Cumuls
            $table->dropColumn('cumul_brut');
            $table->dropColumn('cumul_imposable');
            $table->dropColumn('cumul_net');
            $table->dropColumn('cumul_cotisations_patronales');
        });
    }
};
