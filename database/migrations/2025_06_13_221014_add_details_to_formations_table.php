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
        Schema::table('formations', function (Blueprint $table) {
            $table->text('objectifs_pedagogiques')->nullable()->after('description');
            $table->text('prerequis')->nullable()->after('objectifs_pedagogiques');
            $table->integer('duree_recommandee_heures')->nullable()->after('prerequis');
            $table->string('organisme_formateur')->nullable()->after('duree_recommandee_heures');
            $table->boolean('formateur_interne')->default(false)->after('organisme_formateur');
            $table->decimal('cout', 10, 2)->nullable()->after('formateur_interne');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn([
                'objectifs_pedagogiques',
                'prerequis',
                'duree_recommandee_heures',
                'organisme_formateur',
                'formateur_interne',
                'cout'
            ]);
        });
    }
};
