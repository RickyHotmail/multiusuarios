<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPermisos {
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){
            $ruta = explode('.', $request->route()->getName(),);
            $acceso=DB::table('users')->join('usuario_rol','usuario_rol.user_id','=','users.user_id')->join('rol','rol.rol_id','=','usuario_rol.rol_id')->join('rol_permiso','rol_permiso.rol_id','=','rol.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('users.user_id','=',Auth::user()->user_id)->where('permiso_ruta','like','%'.$ruta[0].'%')->count();
            if ($acceso > 0){
                return $next($request);
            }
            return redirect('/denegado');
        }
        return redirect('/login');
    }
}
