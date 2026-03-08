<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cautela_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cautela_items', 'devolvido_por_id')) {
                $table->foreignId('devolvido_por_id')->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('devolvido');
            }

            if (!Schema::hasColumn('cautela_items', 'devolvido_em')) {
                $table->timestamp('devolvido_em')->nullable()->after('devolvido_por_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cautela_items', function (Blueprint $table) {
            if (Schema::hasColumn('cautela_items', 'devolvido_por_id')) {
                $table->dropForeign(['devolvido_por_id']);
                $table->dropColumn('devolvido_por_id');
            }

            if (Schema::hasColumn('cautela_items', 'devolvido_em')) {
                $table->dropColumn('devolvido_em');
            }
        });
    }
};
