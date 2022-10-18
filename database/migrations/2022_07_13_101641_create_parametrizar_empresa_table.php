<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametrizar_empresa', function (Blueprint $table) {
            $table->id('parametrizar_id');
            $table->string('parametrizar_nombre');
            $table->integer('parametrizar_valor')->default("1");
            $table->integer('parametrizar_estado')->default(1);
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
        Schema::dropIfExists('parametrizar_empresa');
    }
}
