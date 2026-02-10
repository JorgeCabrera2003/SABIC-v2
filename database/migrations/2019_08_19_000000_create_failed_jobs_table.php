<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFailedJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER tr_failed_jobs_insert AFTER INSERT ON failed_jobs
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en failed_jobs', 
                    'failed_jobs', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'uuid', NEW.uuid, 'connection', NEW.connection, 'queue', NEW.queue, 'payload', NEW.payload, 'exception', NEW.exception, 'failed_at', NEW.failed_at)
                );
            END;

            CREATE TRIGGER tr_failed_jobs_update AFTER UPDATE ON failed_jobs
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del failed_jobs ID: ', OLD.id), 
                    'failed_jobs', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'uuid', OLD.uuid, 'connection', OLD.connection, 'queue', OLD.queue, 'payload', OLD.payload, 'exception', OLD.exception, 'failed_at', OLD.failed_at), 
                    JSON_OBJECT('id', NEW.id, 'uuid', NEW.uuid, 'connection', NEW.connection, 'queue', NEW.queue, 'payload', NEW.payload, 'exception', NEW.exception, 'failed_at', NEW.failed_at)
                );
            END;

            CREATE TRIGGER tr_failed_jobs_delete AFTER DELETE ON failed_jobs
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del failed_jobs ID: ', OLD.id), 
                    'failed_jobs', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'uuid', OLD.uuid, 'connection', OLD.connection, 'queue', OLD.queue, 'payload', OLD.payload, 'exception', OLD.exception, 'failed_at', OLD.failed_at), 
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
        Schema::dropIfExists('failed_jobs');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_failed_jobs_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_failed_jobs_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_failed_jobs_delete;");
    }
}
