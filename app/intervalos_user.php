<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class intervalos_user extends Model
{
    protected $table= 'intervalos_users';

    protected $fillable = [
        'id_carbon_interval', 
        'user_id',
        'ctrl',        
    ];
}
