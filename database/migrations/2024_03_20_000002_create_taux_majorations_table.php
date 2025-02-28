<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taux_majorations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('societe_id');
            $table->string('type');  // jour_ferie, dimanche, nuit, etc.
            $table->decimal('base', 10, 2);
            $table->decimal('taux', 5, 2);
            $table->timestamps();

            $table->foreign('societe_id')->references('id')->on('societes')->onDelete('cascade');
            $table->unique(['societe_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taux_majorations');
    }
};
