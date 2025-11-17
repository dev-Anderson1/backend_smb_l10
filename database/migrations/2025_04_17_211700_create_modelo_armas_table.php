<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modelo_armas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('calibre_id');
            $table->string('numero_serie')->nullable();
            $table->timestamps();

            $table->foreign('calibre_id')->references('id')->on('calibres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('modelo_armas');
    }
};
