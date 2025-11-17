<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('armas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('modelo_id')->constrained('modelo_armas')->onDelete('cascade');
    $table->integer('quantidade_carregadores')->default(1);
    $table->string('numero_serie')->unique();
    $table->string('situacao')->default('disponível');
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('armas');
    }
};
