<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasParametrizarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas_parametrizar', function (Blueprint $table) {
            $table->id('parametrizar_id');
            $table->text('parametrizar_nombre');
            $table->bigInteger('parametrizar_orden');
            $table->string('parametrizar_estado');
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
        Schema::dropIfExists('cuentas__parametrizar');
    }
}
