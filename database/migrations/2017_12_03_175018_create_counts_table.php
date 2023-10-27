<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('count_label',100)->nullable();
            $table->integer('user_id')->unsigned();
            $table->text('host')->nullable();
            $table->integer('port')->unsigned()->nullable();
            $table->text('database')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->integer('tipo')->unsigned()->nullable();
            $table->timestamps();

            // Llaves foráneas.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counts');
    }
}
