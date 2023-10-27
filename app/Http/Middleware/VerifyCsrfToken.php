<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
            //[Rogelio R - Workana] - Se agrega la exclusión de las páginas de Login y Logout de la validación del token CRSF, ya que al vencer, genera error en dichas páginas
			//[Luis - Workana] - Se excluye validate_user
            '/logout',
            '/login',
			'/validate_user'
    ];
}
