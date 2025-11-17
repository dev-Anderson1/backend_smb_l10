<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('carregadores', function (Blueprint $table) {
            $table->id();
            $table->integer('capacidade'); // Quantidade de munições suportadas
            $table->integer('quantidade'); // Quantidade total disponível
            $table->timestamps();
        });

        // 🔗 Adicionar relacionamento com modelo_armas
        Schema::table('carregadores', function (Blueprint $table) {
            $table->foreignId('modelo_id')->nullable()
                  ->constrained('modelo_armas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ⚠️ Primeiro, remova a FK antes de dropar a tabela
        Schema::table('carregadores', function (Blueprint $table) {
            $table->dropForeign(['modelo_id']);
            $table->dropColumn('modelo_id');
        });

        Schema::dropIfExists('carregadores');
    }
};
