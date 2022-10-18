<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizarRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametrizar_rol', function (Blueprint $table) {
            $table->id('parametrizar_id');        
            $table->float('parametrizar_dias_trabajo');
            $table->double('parametrizar_sueldo_basico',19,2);
            $table->double('parametrizar_iess_personal',19,2);
            $table->double('parametrizar_iess_patronal',19,2);
            $table->double('parametrizar_fondos_reserva',19,2);
            $table->double('parametrizar_horas_extras',19,2);
            $table->double('parametrizar_iece_secap',19,2); 
            $table->double('parametrizar_porcentaje_he',19,2);  
            $table->double('parametrizar_iess_gerencial',19,2);       
            $table->string('parametrizar_estado');
            $table->bigInteger('empresa_id');
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
        Schema::dropIfExists('parametrizar_rol');
    }
}
