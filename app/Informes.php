<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Informes extends Model
{
    protected $table= 'informes';

    protected $fillable = [
        'check', 
        'emails', 
        'selectcheck',  
        'user_id',        
        'contador',
    ];

    public function _user(){
      return $this->belongsTo(User::class);
    }
}
