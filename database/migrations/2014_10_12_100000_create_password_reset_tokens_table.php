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
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        DB::unprepared("
            CREATE TRIGGER tr_password_reset_tokens_insert AFTER INSERT ON password_reset_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en password_reset_tokens', 
                    'password_reset_tokens', 
                    NEW.email, 
                    NULL, 
                    JSON_OBJECT('email', NEW.email, 'token', NEW.token, 'created_at', NEW.created_at)
                );
            END;

            CREATE TRIGGER tr_password_reset_tokens_update AFTER UPDATE ON password_reset_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del password_reset_tokens email: ', OLD.email), 
                    'password_reset_tokens', 
                    OLD.email, 
                    JSON_OBJECT('email', OLD.email, 'token', OLD.token, 'created_at', OLD.created_at), 
                    JSON_OBJECT('email', NEW.email, 'token', NEW.token, 'created_at', NEW.created_at)
                );
            END;

            CREATE TRIGGER tr_password_reset_tokens_delete AFTER DELETE ON password_reset_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del password_reset_tokens email: ', OLD.email), 
                    'password_reset_tokens', 
                    OLD.email, 
                    JSON_OBJECT('email', OLD.email, 'token', OLD.token, 'created_at', OLD.created_at), 
                    NULL
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_reset_tokens_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_reset_tokens_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_reset_tokens_delete;");
    }
};
