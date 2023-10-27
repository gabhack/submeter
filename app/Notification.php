<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table= 'notifications';

    public function _user(){
      return $this->belongsTo(User::class);
    }
}
