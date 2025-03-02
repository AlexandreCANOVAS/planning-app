<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->boolean('is_special')->default(false)->after('societe_id');
        });

        // Marquer les lieux RH et CP comme spéciaux
        DB::table('lieux')
            ->whereIn('nom', ['RH', 'CP'])
            ->update(['is_special' => true]);
    }

    public function down(): void
    {
        Schema::table('lieux', function (Blueprint $table) {
            $table->dropColumn('is_special');
        });
    }
};
