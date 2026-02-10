<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id();
            $table->string('accion'); // Ej: CREATE, UPDATE, DELETE, LOGIN, etc.
            $table->text('descripcion')->nullable(); // Descripción detallada
            $table->string('tabla_afectada')->nullable(); // Nombre de la tabla afectada
            $table->unsignedBigInteger('registro_id')->nullable(); // ID del registro afectado
            $table->json('valores_anteriores')->nullable(); // Valores antes del cambio
            $table->json('valores_nuevos')->nullable(); // Valores después del cambio
            $table->string('ip_address')->nullable(); // IP del usuario
            $table->string('user_agent')->nullable(); // Navegador/dispositivo
            $table->string('bd_user'); // Usuario de la base de datos
            $table->unsignedBigInteger('user_id')->nullable(); // ID del usuario que realizó la acción
            
            // Relación con la tabla users (si usas esa tabla para usuarios)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps(); // created_at y updated_at
        });

        // 1. Crear el Procedimiento Almacenado
        DB::unprepared("
            DROP PROCEDURE IF EXISTS registrar_bitacora;
            CREATE PROCEDURE registrar_bitacora(
                IN p_accion VARCHAR(255),
                IN p_descripcion TEXT,
                IN p_tabla_afectada VARCHAR(255),
                IN p_registro_id BIGINT,
                IN p_valores_anteriores JSON,
                IN p_valores_nuevos JSON
            )
            BEGIN
                INSERT INTO bitacora (
                    accion,
                    descripcion,
                    tabla_afectada,
                    registro_id,
                    valores_anteriores,
                    valores_nuevos,
                    ip_address,
                    user_agent,
                    bd_user,
                    user_id,
                    created_at,
                    updated_at
                ) VALUES (
                    p_accion,
                    p_descripcion,
                    p_tabla_afectada,
                    p_registro_id,
                    p_valores_anteriores,
                    p_valores_nuevos,
                    @app_ip_address,   -- Variables de sesión enviadas desde Laravel
                    @app_user_agent, 
                    CURRENT_USER(),    -- Usuario de la base de datos (root, user_app, etc)
                    @app_user_id,      -- ID del usuario autenticado en el sistema
                    NOW(),
                    NOW()
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora');
        DB::unprepared("DROP PROCEDURE IF EXISTS registrar_bitacora;");
    }
};