<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MatchesAuthUserId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->tipo === 1) return $next($request);
        
        $idStr = $request->route('user_id');
        $idInt = (int) $idStr;
        
        if (($idStr === (string) $idInt) && $idInt === $user->id) return $next($request);

        return redirect()->back();
    }
}
