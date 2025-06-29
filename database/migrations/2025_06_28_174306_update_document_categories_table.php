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
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_default')->default(false)->after('description');
            $table->unsignedBigInteger('societe_id')->nullable()->after('is_default');
            $table->unsignedBigInteger('created_by')->nullable()->after('societe_id');
            
            $table->foreign('societe_id')->references('id')->on('societes')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            $table->dropForeign(['societe_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['name', 'description', 'is_default', 'societe_id', 'created_by']);
        });
    }
};