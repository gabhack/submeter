<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * [Rogelio R -Workana] - Modelo para la tabla de log de acceso.
 * 
 * Columnas
 * 'user_email' -> username del usuario que está accediendo
 * 'ip_address' -> dirección IP desde la que accede
 * 'address_address' -> Dirección devuenta por la geolocalización
 * 'address_latitude' -> Latitud de la dirección IP (Coordenada Geografica)
 * 'address_longitude' -> Longitud de la dirección IP (Coordenada Geografica)
 * 'access_status' -> Indicador de estatus de conexión, valores esperados: ACCESO, ERROR
 *
 */
class AccessLog extends Model
{
    protected $fillable = [
        'user_email',
        'ip_address',
        'address_address',
        'address_latitude',
        'address_longitude',
        'access_status',
        'local_access_date',
        'local_logout_date',
        'local_timezone_offset',
		'verification_code',
    ];

}
