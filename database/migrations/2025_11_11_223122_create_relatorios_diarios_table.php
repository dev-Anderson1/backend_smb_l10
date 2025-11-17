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
    Schema::create('relatorios_diarios', function (Blueprint $table) {
        $table->id();
        $table->date('data');
        $table->string('instituicao')->nullable();
        $table->foreignId('oficial_dia_id')->nullable()->constrained('users');
        $table->foreignId('respondente_id')->nullable()->constrained('users');
        $table->foreignId('adjunto_id')->nullable()->constrained('users');
        $table->foreignId('dia_smb_id')->nullable()->constrained('users');
        $table->text('acontecimentos')->nullable();
        $table->time('hora_entrada')->nullable();
        $table->time('hora_saida')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorios_diarios');
    }
};
