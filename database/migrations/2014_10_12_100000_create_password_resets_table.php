<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return vod
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        DB::unprepared("
            CREATE TRIGGER tr_password_resets_insert AFTER INSERT ON password_resets
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en password_resets', 
                    'password_resets', 
                    NEW.email, 
                    NULL, 
                    JSON_OBJECT('email', NEW.email, 'token', NEW.token, 'created_at', NEW.created_at)
                );
            END;

            CREATE TRIGGER tr_password_resets_update AFTER UPDATE ON password_resets
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del password_resets email: ', OLD.email), 
                    'password_resets', 
                    OLD.email, 
                    JSON_OBJECT('email', OLD.email, 'token', OLD.token, 'created_at', OLD.created_at), 
                    JSON_OBJECT('email', NEW.email, 'token', NEW.token, 'created_at', NEW.created_at)
                );
            END;

            CREATE TRIGGER tr_password_resets_delete AFTER DELETE ON password_resets
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del password_resets email: ', OLD.email), 
                    'password_resets', 
                    OLD.email, 
                    JSON_OBJECT('email', OLD.email, 'token', OLD.token, 'created_at', OLD.created_at), 
                    NULL
                );
            END;
        ");
    
    }

    /**
     * Reverse the migrations.
     *
     * @return vod
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_resets_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_resets_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_password_resets_delete;");
    }
}
