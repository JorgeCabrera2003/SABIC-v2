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
        Schema::create('nominal_location', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('floor')->nullable();
        });

        DB::unprepared("
            CREATE TRIGGER tr_nominal_location_insert AFTER INSERT ON nominal_location
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en nominal_location', 
                    'nominal_location', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'floor', NEW.floor)
                );
            END;

            CREATE TRIGGER tr_nominal_location_update AFTER UPDATE ON nominal_location
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del nominal_location ID: ', OLD.id), 
                    'nominal_location', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'floor', OLD.floor), 
                    JSON_OBJECT('id', NEW.id, 'name', NEW.name, 'floor', NEW.floor)
                );
            END;

            CREATE TRIGGER tr_nominal_location_delete AFTER DELETE ON nominal_location
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del nominal_location ID: ', OLD.id), 
                    'nominal_location', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'name', OLD.name, 'floor', OLD.floor), 
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
        Schema::dropIfExists('nominal_ubications');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_nominal_location_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_nominal_location_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_nominal_location_delete;");
    }
};
