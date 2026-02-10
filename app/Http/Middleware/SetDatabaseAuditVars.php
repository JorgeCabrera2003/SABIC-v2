<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetDatabaseAuditVars
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo intentamos setear variables si el usuario está autenticado
        if (Auth::check()) {
            $userId = Auth::id();
            $ipAddress = $request->ip();
            $userAgent = substr($request->userAgent(), 0, 255); // Limitamos longitud por seguridad

            /*
            |----------------------------------------------------------------------
            | Variables de Sesión de MySQL
            |----------------------------------------------------------------------
            | Estas variables viven solo durante la duración de la conexión actual.
            | El procedimiento almacenado 'registrar_bitacora' las leerá usando:
            | @app_user_id, @app_ip_address, @app_user_agent
            */
            DB::statement("SET @app_user_id = ?, @app_ip_address = ?, @app_user_agent = ?", [
                $userId,
                $ipAddress,
                $userAgent
            ]);
        }

        return $next($request);
    }
}