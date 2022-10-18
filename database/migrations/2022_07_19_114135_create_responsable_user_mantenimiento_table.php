<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsableUserMantenimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsable_user_mantenimiento', function (Blueprint $table) {
            $table->id('responsable_user_id');
            $table->integer('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->integer('empleado_id')->nullable();
            $table->string('responsable_user_nombre',100);
            $table->string('responsable_user_apellido',100);
            $table->string('responsable_user_cedula',10);
            $table->string('responsable_user_telefono',15);
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
        Schema::dropIfExists('responsable_user_mantenimiento');
    }
}
