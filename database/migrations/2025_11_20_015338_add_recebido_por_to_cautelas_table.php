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
    Schema::table('cautelas', function (Blueprint $table) {
        if (!Schema::hasColumn('cautelas', 'recebido_por')) {
            $table->string('recebido_por')->nullable()->after('devolvido_por_id');
        }

        // $table->foreign('devolvido_por_id')
        //     ->references('id')
        //     ->on('users')
        //     ->nullOnDelete();
    });
}

public function down()
{
    Schema::table('cautelas', function (Blueprint $table) {
        $table->dropForeign(['devolvido_por_id']);
        $table->dropColumn(['devolvido_por_id', 'recebido_por']);
    });
}

};
