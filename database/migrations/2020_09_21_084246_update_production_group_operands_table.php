<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductionGroupOperandsTable extends Migration
{
    # @Leo W* dos nuevos campos en los opradores de agrupadores 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_group_operands', function (Blueprint $table) {
            $table->string('field_database')->default('')->after('field_content');
        });

        Schema::table('production_group_operands', function (Blueprint $table) {
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
        if (Schema::hasColumn('production_group_operands', 'field_database'))
        {
            Schema::table('production_group_operands', function (Blueprint $table) {
                $table->dropColumn('field_database');
            });    
        }

        if (Schema::hasColumn('production_group_operands', 'field_table'))
        {
            Schema::table('production_group_operands', function (Blueprint $table) {
                $table->dropColumn('field_table');
            });    
        }

        /*
        alter table `production_configurations` 
        add `chart_type` enum('line', 'bar', 'area', 'pie') not null default 'line' after `color`, 
        add `chart_interval_daily` int unsigned not null default '60' after `chart_type`, 
        add `chart_interval_weekly` int unsigned not null default '60' after `chart_interval_daily`;

        alter table `energy_meters` add `production_databases` json null after `group_id`;

        alter table `production_field_operands` add `field_database` varchar(191) not null default '' after `field_content`;
        alter table `production_field_operands` add `field_table` varchar(191) not null default '' after `field_database`;

        alter table `production_group_operands` add `field_database` varchar(191) not null default '' after `field_content`;
        alter table `production_group_operands` add `field_table` varchar(191) not null default '' after `field_database`;

        UPDATE field_operations SET MAX_operands = 9999 WHERE NAME <> 'NUMERO';

        /*UPDATE energy_meters a ,
            (SELECT  CONCAT('[{
        "id":"1",
        "name":"',c.database,'",
        "host":"',c.host,'",
        "port":"',c.port,'",
        "database":"',c.database,'",
        "username":"',c.username,'",
        "password":"',c.password,'"}]') AS data FROM energy_meters c ) b
            SET a.production_databases = b.data;


        UPDATE production_field_operands a 
        LEFT JOIN production_fields b ON a.production_field_id = b.id
        LEFT JOIN production_configurations c ON b.configuration_id = c.id
        SET a.field_database = '1',a.field_table = ifnull(c.table_name,'');
        
        UPDATE production_group_operands a 
        LEFT JOIN production_group_fields b ON a.production_group_field_id = b.id
        LEFT JOIN production_configurations c ON b.configuration_id = c.id
        SET a.field_database = '1',a.field_table = ifnull(c.table_name,'')
        */
    }

}
