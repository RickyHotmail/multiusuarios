<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportistaMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transportista_movil', function (Blueprint $table) {
            $table->id('transportista_id');
            $table->string('transportista_nombre', 200);
            $table->integer('transportista_tipo_identificacion');
            $table->string('transportista_identificacion', 25);
            $table->string('transportista_correo', 100);
            $table->string('transportista_placa', 20);
            $table->integer('transportista_estado')->default(1);
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
        Schema::dropIfExists('transportista_movil');
    }
}
