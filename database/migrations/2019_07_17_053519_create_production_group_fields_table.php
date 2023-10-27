<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionGroupFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_group_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->string("display_name");
            $table->unsignedInteger("configuration_id");
            $table->unsignedInteger("production_type_id");
            $table->unsignedInteger("show_type_id");
            $table->unsignedInteger("operation_id");
            $table->unsignedInteger("number_type_id");
            $table->unsignedInteger("decimal_count")->default(0);
            $table->string("units")->nullable();
            $table->string("color")->nullable();
            $table->boolean("updated")->default(1);
            $table->timestamps();
            
            $table->foreign('configuration_id')->references('id')->on('production_configurations')->onDelete('cascade');
            $table->foreign('production_type_id')->references('id')->on('production_types')->onDelete('cascade');
            $table->foreign('number_type_id')->references('id')->on('number_types')->onDelete('cascade');
            $table->foreign('operation_id')->references('id')->on('group_operations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_group_fields');
    }
}
