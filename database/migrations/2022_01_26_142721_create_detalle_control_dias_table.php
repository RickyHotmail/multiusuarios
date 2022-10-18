<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleControlDiasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_control_dias', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->string('control_dia1', 1);
            $table->string('control_dia2', 1);
            $table->string('control_dia3', 1);
            $table->string('control_dia4', 1);
            $table->string('control_dia5', 1);
            $table->string('control_dia6', 1);
            $table->string('control_dia7', 1);
            $table->string('control_dia8', 1);
            $table->string('control_dia9', 1);
            $table->string('control_dia10', 1);
            $table->string('control_dia11', 1);
            $table->string('control_dia12', 1);
            $table->string('control_dia13', 1);
            $table->string('control_dia14', 1);
            $table->string('control_dia15', 1);
            $table->string('control_dia16', 1);
            $table->string('control_dia17', 1);
            $table->string('control_dia18', 1);
            $table->string('control_dia19', 1);
            $table->string('control_dia20', 1);
            $table->string('control_dia21', 1);
            $table->string('control_dia22', 1);
            $table->string('control_dia23', 1);
            $table->string('control_dia24', 1);
            $table->string('control_dia25', 1);
            $table->string('control_dia26', 1);
            $table->string('control_dia27', 1);
            $table->string('control_dia28', 1);
            $table->string('control_dia29', 1);
            $table->string('control_dia30', 1);
            $table->string('control_dia31', 1);
            $table->string('detalle_estado');
            $table->bigInteger('control_id');
            $table->foreign('control_id')->references('control_id')->on('control_dias');
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
        Schema::dropIfExists('detalle_control_dias');
    }
}
