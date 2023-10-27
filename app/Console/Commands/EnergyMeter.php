<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnergyMeter extends Model
{
    protected $table= 'energy_meters';

    protected $fillable = [
        'count_label',
    	'host',
        'port',
        'database',
        'username',
        'password',
        'tipo', 
        'subtipo', 
        'tarifa', 
        'iee', 
        'group_id', 
        'production_databases'
    ];
}
