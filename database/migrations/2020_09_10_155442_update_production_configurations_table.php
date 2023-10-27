<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductionConfigurationsTable extends Migration
{
    # @Leo W* dos nuevos campos en la configuracion de produccion 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_configurations', function (Blueprint $table) {
            $table->enum('chart_type', ['line', 'bar','area','pie'])->default('line')->after('color');
            $table->unsignedInteger('chart_interval_daily')->default(60)->after('chart_type');
            $table->unsignedInteger('chart_interval_weekly')->default(60)->after('chart_interval_daily');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('production_configurations', 'chart_type'))
        {
            Schema::table('production_configurations', function (Blueprint $table) {
                $table->dropColumn('chart_type');
            });    
        }
        if (Schema::hasColumn('production_configurations', 'chart_interval_daily'))
        {
            Schema::table('production_configurations', function (Blueprint $table) {
                $table->dropColumn('chart_interval_daily');
            });    
        }
        if (Schema::hasColumn('production_configurations', 'chart_interval_weekly'))
        {
            Schema::table('production_configurations', function (Blueprint $table) {
                $table->dropColumn('chart_interval_weekly');
            });    
        }
        
    }

    /*
    alter table `production_configurations` add `chart_type` enum('line', 'bar', 'area', 'pie') not null default 'line' after `color`, add `chart_interval_daily` int unsigned not null default '60' after `chart_type`, add `chart_interval_weekly` int unsigned not null default '60' after `chart_interval_daily`
    */
}
