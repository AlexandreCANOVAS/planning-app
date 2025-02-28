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
        Schema::table('lieux', function (Blueprint $table) {
            $table->string('couleur')->nullable()->after('description')->default('#3498db');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->dropColumn('couleur');
        });
    }
};
