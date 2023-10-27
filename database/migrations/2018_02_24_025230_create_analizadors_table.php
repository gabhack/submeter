<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalizadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analizadors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->unique();
            $table->integer('count_id')->unsigned();
            $table->text('host')->nullable();
            $table->integer('port')->unsigned()->nullable();
            $table->text('database')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->integer('principal')->unsigned();
            $table->text('color_etiqueta')->nullable();
            $table->timestamps();

            $table->foreign('count_id')->references('id')->on('counts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analizadors');
    }
}
