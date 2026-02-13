<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->string('document');
            $table->string('name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->foreignId('id_nominal_location')->constrained('nominal_location')->onDelete('cascade');
            $table->foreignId('id_position')->constrained('position')->onDelete('cascade');
            $table->string('photo_dir')->default('default.png');
            $table->enum('status', ['active', 'inactive', 'vacation', 'authorized', 'unauthorized'])->default('active');
        });

        DB::unprepared("
            CREATE TRIGGER tr_personals_insert AFTER INSERT ON personals
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'CREATE', 
                    'Nuevo registro creado en personals', 
                    'personals', 
                    NEW.id, 
                    NULL, 
                    JSON_OBJECT('id', NEW.id, 'document', NEW.document, 'name', NEW.name, 'last_name', NEW.last_name, 'phone_number', NEW.phone_number, 'email', NEW.email, 'id_nominal_location', NEW.id_nominal_location, 'id_position', NEW.id_position, 'photo_dir', NEW.photo_dir, 'status', NEW.status)
                );
            END;

            CREATE TRIGGER tr_personals_update AFTER UPDATE ON personals
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'UPDATE', 
                    CONCAT('Actualización del personal ID: ', OLD.id), 
                    'personals', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'document', OLD.document, 'name', OLD.name, 'last_name', OLD.last_name, 'phone_number', OLD.phone_number, 'email', OLD.email, 'id_nominal_location', OLD.id_nominal_location, 'id_position', OLD.id_position, 'photo_dir', OLD.photo_dir, 'status', OLD.status), 
                    JSON_OBJECT('id', NEW.id, 'document', NEW.document, 'name', NEW.name, 'last_name', NEW.last_name, 'phone_number', NEW.phone_number, 'email', NEW.email, 'id_nominal_location', NEW.id_nominal_location, 'id_position', NEW.id_position, 'photo_dir', NEW.photo_dir, 'status', NEW.status)
                );
            END;

            CREATE TRIGGER tr_personals_delete AFTER DELETE ON personals
            FOR EACH ROW
            BEGIN
                CALL registrar_bitacora(
                    'DELETE', 
                    CONCAT('Eliminación del personal ID: ', OLD.id), 
                    'personals', 
                    OLD.id, 
                    JSON_OBJECT('id', OLD.id, 'document', OLD.document, 'name', OLD.name, 'last_name', OLD.last_name, 'phone_number', OLD.phone_number, 'email', OLD.email, 'id_nominal_location', OLD.id_nominal_location, 'id_position', OLD.id_position, 'photo_dir', OLD.photo_dir, 'status', OLD.status), 
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
        Schema::dropIfExists('personals');
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personals_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personals_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_personals_delete;");
    }
}
