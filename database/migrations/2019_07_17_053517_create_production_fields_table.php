<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->unsignedInteger("configuration_id");
            $table->unsignedInteger("operation_id");            
            $table->boolean("updated")->default(1);
            $table->timestamps();
            
            $table->foreign('configuration_id')->references('id')->on('production_configurations')->onDelete('cascade');
            $table->foreign('operation_id')->references('id')->on('field_operations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_fields');
    }
}
