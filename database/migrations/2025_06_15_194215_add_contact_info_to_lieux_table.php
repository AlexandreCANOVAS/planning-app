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
            $table->string('telephone')->nullable()->after('code_postal');
            $table->string('horaires')->nullable()->after('telephone');
            $table->string('contact_principal')->nullable()->after('horaires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'horaires', 'contact_principal']);
        });
    }
};
