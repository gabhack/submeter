<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionFieldOperandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_field_operands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("production_field_id");
            $table->unsignedInteger("field_type_id");
            $table->string("field_content")->nullable();
            $table->boolean("updated")->default(1);
            $table->timestamps();
            
            $table->foreign('production_field_id')->references('id')->on('production_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_field_operands');
    }
}
