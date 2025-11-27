<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reservas_municoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // instrutor que solicitou
            $table->foreignId('calibre_id')->constrained('calibres')->onDelete('cascade');
            $table->string('turma')->nullable();
            $table->string('tipo_aula')->nullable();
            $table->integer('alunos')->default(1);
            $table->integer('municoes_por_aluno')->default(1);
            $table->integer('total_municoes')->default(0);
            $table->string('status')->default('pending'); // pending, approved, cancelled, completed
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cautela_numero')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas_municoes');
    }
};
