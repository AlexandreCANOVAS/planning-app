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
        Schema::table('plannings', function (Blueprint $table) {
            $table->string('periode')->default('journee');
            $table->string('heures_travaillees')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->dropColumn('periode');
            $table->float('heures_travaillees')->change();
        });
    }
};
