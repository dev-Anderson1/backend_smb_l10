<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cautela_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cautela_items', 'devolvido')) {
                $table->boolean('devolvido')->default(false)->after('quantidade');
            }
        });
    }

    public function down()
    {
        Schema::table('cautela_items', function (Blueprint $table) {
            $table->dropColumn('devolvido');
        });
    }
};
