<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionGroupOperandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_group_operands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("production_group_field_id");
            $table->unsignedInteger("field_type_id");
            $table->string("field_content")->nullable();
            $table->boolean("updated")->default(1);
            $table->timestamps();
            
            $table->foreign('production_group_field_id')->references('id')->on('production_group_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_group_operands');
    }
}
