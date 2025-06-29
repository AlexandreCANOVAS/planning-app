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
        Schema::table('document_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('document_categories', 'societe_id')) {
                $table->unsignedBigInteger('societe_id')->nullable()->after('is_default');
                $table->foreign('societe_id')->references('id')->on('societes')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('document_categories', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('societe_id');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            if (Schema::hasColumn('document_categories', 'societe_id')) {
                $table->dropForeign(['societe_id']);
                $table->dropColumn('societe_id');
            }
            
            if (Schema::hasColumn('document_categories', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
