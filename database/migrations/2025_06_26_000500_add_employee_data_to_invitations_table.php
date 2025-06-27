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
        Schema::table('employee_invitations', function (Blueprint $table) {
            $table->string('nom')->after('token');
            $table->string('prenom')->after('nom');
            $table->string('poste')->nullable()->after('prenom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_invitations', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'poste']);
        });
    }
};
