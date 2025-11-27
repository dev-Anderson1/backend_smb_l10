<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('municao_saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('calibre_id')->constrained('calibres')->onDelete('cascade');
            $table->integer('quantidade')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'calibre_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('municao_saldos');
    }
};
