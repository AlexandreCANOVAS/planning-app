<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employe_formation', function (Blueprint $table) {
            $table->date('last_recyclage')->nullable()->after('date_recyclage');
        });
    }

    public function down(): void
    {
        Schema::table('employe_formation', function (Blueprint $table) {
            $table->dropColumn('last_recyclage');
        });
    }
};
