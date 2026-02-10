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
        Schema::create('position', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        DB::unprepared("
            CREATE TRIGGER tr_position_insert AFTER INSERT ON position
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en position', 
                    'position', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name)
                );
            END;

            CREATE TRIGGER tr_position_update AFTER UPDATE ON position
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del position ID: ', OLD.id), 
                    'position', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name), 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name)
                );
            END;

            CREATE TRIGGER tr_position_delete AFTER DELETE ON position
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del position ID: ', OLD.id), 
                    'position', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name), 
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
        Schema::dropIfExists('position');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_position_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_position_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_position_delete;");
    }
};
