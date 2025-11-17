<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatorios_diarios', function (Blueprint $table) {
            $table->foreignId('dia_smb_entrada_id')->nullable()->after('dia_smb_id')->constrained('users');
            $table->foreignId('dia_smb_saida_id')->nullable()->after('dia_smb_entrada_id')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('relatorios_diarios', function (Blueprint $table) {
            $table->dropForeign(['dia_smb_entrada_id']);
            $table->dropForeign(['dia_smb_saida_id']);
            $table->dropColumn(['dia_smb_entrada_id', 'dia_smb_saida_id']);
        });
    }
};
