<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente_movil', function (Blueprint $table) {
            $table->id('cliente_id');
            $table->string("cliente_nombre", 50);
            $table->string("cliente_apellido", 50);
            $table->integer("cliente_tipo");
            $table->integer("cliente_tipo_identificacion");
            $table->string("cliente_identificacion", 50);
            $table->string("cliente_correo", 100);
            $table->string("cliente_direccion", 200);
            $table->string("cliente_telefono_convencional", 10);
            $table->string("cliente_telefono_extension", 10);
            $table->string("cliente_telefono_celular", 10)->nullable();
            $table->integer('cliente_estado')->default(1);
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
        Schema::dropIfExists('cliente_movil');
    }
}
