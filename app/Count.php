<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Count extends Model
{
    protected $table= 'counts';

    protected $fillable = [
        'tipo'
    ];

    public function _user(){
      return $this->belongsTo(User::class);
    }

    public function _analizador()
    {
        return $this->hasMany(Analizador::class);
    }
    
    public function grupo()
    {
        return $this->hasOne('App\Groups', 'id', 'group_id');
    }
}
