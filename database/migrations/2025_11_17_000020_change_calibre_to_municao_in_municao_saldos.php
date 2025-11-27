<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('municao_saldos', function (Blueprint $table) {
            // adiciona coluna municao_id (nullable temporariamente)
            $table->foreignId('municao_id')->nullable()->after('user_id')->constrained('municoes')->nullOnDelete();
            // remove coluna calibre_id se existir
            if (Schema::hasColumn('municao_saldos', 'calibre_id')) {
                $table->dropForeign(['calibre_id']);
                $table->dropColumn('calibre_id');
            }
        });
    }

    public function down()
    {
        Schema::table('municao_saldos', function (Blueprint $table) {
            // recria calibre_id (nullable)
            $table->foreignId('calibre_id')->nullable()->after('user_id')->constrained('calibres')->nullOnDelete();
            if (Schema::hasColumn('municao_saldos', 'municao_id')) {
                $table->dropForeign(['municao_id']);
                $table->dropColumn('municao_id');
            }
        });
    }
};
