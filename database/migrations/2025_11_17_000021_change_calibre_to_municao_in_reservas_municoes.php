<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reservas_municoes', function (Blueprint $table) {
            // adiciona municao_id
            $table->foreignId('municao_id')->nullable()->after('calibre_id')->constrained('municoes')->nullOnDelete();
            // remove calibre_id se existir
            if (Schema::hasColumn('reservas_municoes', 'calibre_id')) {
                $table->dropForeign(['calibre_id']);
                $table->dropColumn('calibre_id');
            }
        });
    }

    public function down()
    {
        Schema::table('reservas_municoes', function (Blueprint $table) {
            $table->foreignId('calibre_id')->nullable()->after('municao_id')->constrained('calibres')->nullOnDelete();
            if (Schema::hasColumn('reservas_municoes', 'municao_id')) {
                $table->dropForeign(['municao_id']);
                $table->dropColumn('municao_id');
            }
        });
    }
};
