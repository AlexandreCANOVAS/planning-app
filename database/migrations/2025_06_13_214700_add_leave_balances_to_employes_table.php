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
            $table->decimal('solde_conges', 5, 1)->default(25.0);
            $table->decimal('solde_rtt', 5, 1)->default(0.0);
            $table->decimal('solde_conges_exceptionnels', 5, 1)->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn('solde_conges');
            $table->dropColumn('solde_rtt');
            $table->dropColumn('solde_conges_exceptionnels');
        });
    }
};
