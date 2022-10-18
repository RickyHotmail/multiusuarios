<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id('auditoria_id');
            $table->date('auditoria_fecha');
            $table->time('auditoria_hora');
            $table->string('auditoria_maquina');
            $table->text('auditoria_adicional');
            $table->text('auditoria_descripcion');
            $table->string('auditoria_numero_documento');
            $table->string('auditoria_estado');
            $table->integer('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');  
            $table->timestamps();
            $table->text('auditoria_documento_respaldo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditoria');
    }
}
