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
        $table->unsignedBigInteger('devolvido_por_id')->nullable()->after('user_confirm_id');
        $table->foreign('devolvido_por_id')->references('id')->on('users');
    });
}

public function down()
{
    Schema::table('cautelas', function (Blueprint $table) {
        $table->dropForeign(['devolvido_por_id']);
        $table->dropColumn('devolvido_por_id');
    });
}

};
