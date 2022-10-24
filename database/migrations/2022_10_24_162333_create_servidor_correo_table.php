<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServidorCorreoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servidor_correo', function (Blueprint $table) {
            $table->id('servidor_id');
            $table->text('servidor_host');
            $table->text('servidor_username');
            $table->text('servidor_password');
            $table->text('servidor_from');
            $table->text('servidor_embeddedImage');
            $table->integer('servidor_port');
            $table->text('servidor_secure');
            $table->integer('servidor_estado');
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
        Schema::dropIfExists('servidor_correo');
    }
}
