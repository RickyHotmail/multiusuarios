<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmaElectronicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firma_electronica', function (Blueprint $table) {
            $table->id('firma_id'); 
            $table->string('firma_ambiente');
            $table->string('firma_archivo')->nullable(); 
            $table->string('firma_password')->nullable();
            $table->text('firma_pubKey')->nullable();
            $table->text('firma_privKey')->nullable();
            $table->date('firma_fecha');
            $table->string('firma_disponibilidad');
            $table->string('firma_estado');            
            $table->integer('empresa_id');
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
        Schema::dropIfExists('firma_electronica');
    }
}
