<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizacionGrupoPermisoTable extends Migration{
    public function up()
    {
        Schema::create('parametrizacion_grupo_permiso', function (Blueprint $table) {
            $table->id('parametrizaciong_id');
            $table->string('parametrizaciong_nombre');
            $table->integer('parametrizaciong_estado')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametrizacion_grupo_permiso');
    }
}