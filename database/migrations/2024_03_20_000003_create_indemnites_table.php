<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('indemnites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->decimal('base', 10, 2);
            $table->decimal('taux', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('indemnites');
    }
};
