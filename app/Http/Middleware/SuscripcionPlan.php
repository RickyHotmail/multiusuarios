<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuscripcionPlan
{
    public function handle(Request $request, Closure $next)
    {
        $suscripcion=Auth::user()->empresa->suscripcion;
        $hoy = date("Y-m-d");

        if($suscripcion){
            if($suscripcion->suscripcion_fecha_finalizacion >= $hoy) return $next($request);
            
            return redirect('/denegado');
        }

        return $next($request);
    }
}
