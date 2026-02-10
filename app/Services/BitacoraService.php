<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Personal;
use Illuminate\Support\Facades\Auth;

class BitacoraService
{
    public static function registrar($accion, $descripcion, $tabla = null, $registroId = null, $valoresAnteriores = null, $valoresNuevos = null, $cedulaUsuario = null)
    {
        // Buscar usuario por cédula si no hay sesión activa
        $userId = null;
        
        if ($cedulaUsuario) {
            $personal = Personal::where('cedula', $cedulaUsuario)->first();
            if ($personal) {
                $userId = $personal->id;
            }
        }
        
        // Si no se encontró por cédula y hay sesión activa, usar el usuario autenticado
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        Bitacora::create([
            'accion' => $accion,
            'descripcion' => $descripcion,
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'valores_anteriores' => $valoresAnteriores,
            'valores_nuevos' => $valoresNuevos,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function registroSimple($accion, $descripcion, $cedulaUsuario = null)
    {
        self::registrar($accion, $descripcion, null, null, null, null, $cedulaUsuario);
    }

    public static function registroCRUD($accion, $modelo, $registroId, $valoresAnteriores = null, $valoresNuevos = null, $cedulaUsuario = null)
    {
        $descripcion = self::generarDescripcionCRUD($accion, $modelo, $registroId);
        self::registrar($accion, $descripcion, $modelo, $registroId, $valoresAnteriores, $valoresNuevos, $cedulaUsuario);
    }

    private static function generarDescripcionCRUD($accion, $modelo, $registroId)
    {
        $descripciones = [
            'CREATE' => "Creación de registro en {$modelo}",
            'UPDATE' => "Actualización de registro #{$registroId} en {$modelo}",
            'DELETE' => "Eliminación de registro #{$registroId} en {$modelo}",
            'RESTORE' => "Restauración de registro #{$registroId} en {$modelo}"
        ];

        return $descripciones[$accion] ?? "Acción {$accion} en {$modelo}";
    }
}