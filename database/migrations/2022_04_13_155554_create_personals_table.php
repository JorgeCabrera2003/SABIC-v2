<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->integer('cedula');
            $table->string('nombre');
            $table->string('apellido');
            $table->integer('telefono');
            $table->string('email');
            $table->string('ubicacion_nominal');
            $table->string('cargo');
            $table->string('foto_dir');
            $table->string('curriculo_dir');
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
        Schema::dropIfExists('personals');
    }
}
