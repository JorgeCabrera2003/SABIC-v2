<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_personal')->constrained('personals')->onDelete('cascade');
            $table->date('day')->currentDate();
            $table->time('hour')->currentTime();
            $table->text('observation')->nullable();
            $table->enum('record_type', ['HUELLA', 'MANUAL']);
        });

        DB::unprepared("
            CREATE TRIGGER tr_asistencias_insert AFTER INSERT ON attendance
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en attendance', 
                    'attendance', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'id_personal', NEW.id_personal, 'day', NEW.day, 'hour', NEW.hour, 'observation', NEW.observation, 'record_type', NEW.record_type)
                );
            END;

            CREATE TRIGGER tr_asistencias_update AFTER UPDATE ON attendance
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización de la asistencia ID: ', OLD.id), 
                    'attendance', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'id_personal', OLD.id_personal, 'day', OLD.day, 'hour', OLD.hour, 'observation', OLD.observation, 'record_type', OLD.record_type), 
                    JSON_OBJECT('id', NEW.id, 'id_personal', NEW.id_personal, 'day', NEW.day, 'hour', NEW.hour, 'observation', NEW.observation, 'record_type', NEW.record_type)
                );
            END;

            CREATE TRIGGER tr_asistencias_delete AFTER DELETE ON attendance
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación de la asistencia ID: ', OLD.id), 
                    'attendance', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'id_personal', OLD.id_personal, 'day', OLD.day, 'hour', OLD.hour, 'observation', OLD.observation, 'record_type', OLD.record_type), 
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
        Schema::dropIfExists('asistencias');
    }
}
