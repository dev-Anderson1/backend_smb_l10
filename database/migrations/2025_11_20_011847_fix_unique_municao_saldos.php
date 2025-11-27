<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('municao_saldos', function (Blueprint $table) {

            // 1️⃣ Remover FK que usa o índice errado
            $table->dropForeign('municao_saldos_user_id_foreign');

            // 2️⃣ Agora sim: remover o índice errado
            $table->dropUnique('municao_saldos_user_id_calibre_id_unique');

            // 3️⃣ Recriar a foreign key corretamente
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // 4️⃣ Criar o UNIQUE correto
            $table->unique(
                ['user_id', 'municao_id', 'turma_id', 'tipo_aula_id'],
                'saldo_unico_por_instrutor'
            );
        });
    }

    public function down(): void
    {
        Schema::table('municao_saldos', function (Blueprint $table) {

            // remover o unique novo
            $table->dropUnique('saldo_unico_por_instrutor');

            // remover FK nova
            $table->dropForeign('municao_saldos_user_id_foreign');

            // recriar índice único errado (rollback)
            $table->unique('user_id', 'municao_saldos_user_id_calibre_id_unique');

            // recriar FK antiga
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
