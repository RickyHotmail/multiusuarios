<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_username')->unique();
            $table->string('user_cedula')->unique();
            $table->string('user_nombre');
            $table->string('user_correo');
            $table->string('user_tipo');
            $table->string('user_estado')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->integer('empresa_id')->nullable();
            $table->foreign('empresa_id')->references('empresa_id')->on('empresa');
            $table->timestamps();
            $table->integer('user_cambio_clave')->default(0);
            $table->string('user_fcm_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
