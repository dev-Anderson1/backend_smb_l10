<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('municao_saldos', function (Blueprint $table) {
            $table->foreignId('turma_id')->nullable()->after('municao_id')->constrained('turmas')->nullOnDelete();
            $table->foreignId('tipo_aula_id')->nullable()->after('turma_id')->constrained('tipo_aulas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('municao_saldos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_aula_id');
            $table->dropConstrainedForeignId('turma_id');
        });
    }
};
