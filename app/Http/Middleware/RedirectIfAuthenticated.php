<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // dd($request->session()->all()['_flash']['old']);
        if (Auth::guard($guard)->check()) {            
            return redirect('/home');
        }
        $interval = 1;
        $sesion = $request->session()->all();
        if(!isset($sesion['_flash']))
        {
            $sesion['_flash'] = array('old' => array(),'new' => array(), 'intervalos' => 1 );
            
        }
        $flash = $sesion['_flash'];
        $flash['intervalos'] = $interval; 
        Session::put('_flash',$flash);
        return $next($request);
    }
}
