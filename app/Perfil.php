<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table= 'perfils';

    protected $fillable = [
        'direccion', 
        'fijo',
        'movil',
        'avatar',   
        'user_id',
        'denominacion_social', 
        'domicilio_social', 
        'domicilio_suministro', 
        'cups', 
        'cif', 
        'empresa_distribuidora', 
        'empresa_comercializadora', 
        'persona_contacto', 
        'tarifa',
    ];

    public function _user(){
      return $this->hasOne(User::class);
    }
}
