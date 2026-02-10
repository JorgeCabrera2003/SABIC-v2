<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER tr_users_insert AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en users', 
                    'users', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('name', NEW.name, 'email', NEW.email, 'email_verified_at', NEW.email_verified_at, 'password', NEW.password, 'remember_token', NEW.remember_token, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at)
                );
            END;

            CREATE TRIGGER tr_users_update AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del usuario ID: ', OLD.id), 
                    'users', 
                    OLD.id, 
                    JSON_OBJECT('name', OLD.name, 'email', OLD.email, 'email_verified_at', OLD.email_verified_at, 'password', OLD.password, 'remember_token', OLD.remember_token, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at), 
                    JSON_OBJECT('name', NEW.name, 'email', NEW.email, 'email_verified_at', NEW.email_verified_at, 'password', NEW.password, 'remember_token', NEW.remember_token, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at)
                );
            END;

            CREATE TRIGGER tr_users_delete AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del usuario ID: ', OLD.id), 
                    'users', 
                    OLD.id, 
                    JSON_OBJECT('name', OLD.name, 'email', OLD.email, 'email_verified_at', OLD.email_verified_at, 'password', OLD.password, 'remember_token', OLD.remember_token, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at), 
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
        Schema::dropIfExists('users');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_users_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_users_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_users_delete;");
    }
}
