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
            $table->string('document');
            $table->string('name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->foreignId('id_nominal_location')->constrained('nominal_location')->onDelete('cascade');
            $table->foreignId('id_position')->constrained('position')->onDelete('cascade');
            $table->string('photo_dir')->default('fotos-personal/default.png');
            $table->enum('status', ['active', 'inactive', 'vacation', 'authorized', 'unauthorized'])->default('active');
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
