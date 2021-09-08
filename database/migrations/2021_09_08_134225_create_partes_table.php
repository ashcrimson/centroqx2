<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partes', function (Blueprint $table) {
            $table->id('id');
            $table->string('rut_paciente');
            $table->string('nombre_paciente');
            $table->string('apellido_paciente');
            $table->string('telefono_paciente');
            $table->decimal('edad_paciente');
            $table->string('tipo_cirugia');
            $table->string('especialidad');
            $table->string('diagnostico');
            $table->string('otro_diagnostico');
            $table->string('intervencion');
            $table->string('lateralidad');
            $table->string('otra_intervencion');
            $table->string('cma');
            $table->string('clasificacion_asa');
            $table->decimal('tiempo_quirurgico');
            $table->string('anestecia_sugerida');
            $table->boolean('aislamiento');
            $table->boolean('alergia_latex');
            $table->boolean('usuario_taco');
            $table->boolean('necesidad_cama_upc');
            $table->boolean('prioridad');
            $table->boolean('necesita_donantes_sangre');
            $table->boolean('evaluacion_preanestesica');
            $table->boolean('equipo_rayos');
            $table->boolean('insumos_especificos');
            $table->string('ex_preoperatorios');
            $table->boolean('biopsia');
            $table->string('instrumental');
            $table->string('observaciones');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('partes');
    }
}
