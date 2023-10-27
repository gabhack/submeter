<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductionFieldOperandsTable extends Migration
{
    # @Leo W* dos nuevos campos en los operadores de fields
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_field_operands', function (Blueprint $table) {
            $table->string('field_database')->default('')->after('field_content');
        });

        Schema::table('production_field_operands', function (Blueprint $table) {
            $table->string('field_table')->default('')->after('field_database');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('production_field_operands', 'field_database'))
        {
            Schema::table('production_field_operands', function (Blueprint $table) {
                $table->dropColumn('field_database');
            });    
        }

        if (Schema::hasColumn('production_field_operands', 'field_table'))
        {
            Schema::table('production_field_operands', function (Blueprint $table) {
                $table->dropColumn('field_table');
            });    
        }

        /*
        alter table `production_field_operands` add `field_database` varchar(191) not null default '' after `field_content`;
        alter table `production_field_operands` add `field_table` varchar(191) not null default '' after `field_database`;
        */
    }
}
