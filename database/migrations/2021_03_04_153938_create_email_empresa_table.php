<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_empresa', function (Blueprint $table) {
            $table->id('email_id'); 
            $table->string('email_servidor');
            $table->string('email_email'); 
            $table->string('email_usuario');
            $table->string('email_pass');
            $table->string('email_puerto'); 
            $table->string('email_mensaje');
            $table->string('email_estado');
            $table->integer('empresa_id');
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_empresa');
    }
}
