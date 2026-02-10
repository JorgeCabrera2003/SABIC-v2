<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePersonalAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER tr_personal_access_tokens_insert AFTER INSERT ON personal_access_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en personal_access_tokens', 
                    'personal_access_tokens', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'token', NEW.token, 'abilities', NEW.abilities, 'last_used_at', NEW.last_used_at)
                );
            END;

            CREATE TRIGGER tr_personal_access_tokens_update AFTER UPDATE ON personal_access_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del personal_access_tokens ID: ', OLD.id), 
                    'personal_access_tokens', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'token', OLD.token, 'abilities', OLD.abilities, 'last_used_at', OLD.last_used_at), 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'token', NEW.token, 'abilities', NEW.abilities, 'last_used_at', NEW.last_used_at)
                );
            END;

            CREATE TRIGGER tr_personal_access_tokens_delete AFTER DELETE ON personal_access_tokens
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del personal_access_tokens ID: ', OLD.id), 
                    'personal_access_tokens', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'token', OLD.token, 'abilities', OLD.abilities, 'last_used_at', OLD.last_used_at), 
                    NULL
                );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personal_access_tokens_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personal_access_tokens_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personal_access_tokens_delete;");
    }
}
