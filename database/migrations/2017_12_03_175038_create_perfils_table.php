<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfils', function (Blueprint $table) {
            $table->engine = 'InnoDB';            
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('direccion',100)->nullable(); //Campo para introducir la direccion del usuario.            
            $table->string('fijo',100)->nullable(); // Número de teléfono fijo.
            $table->string('movil',100)->nullable(); // Número de teléfono móvil.
            $table->string('denominacion_social',100)->nullable();
            $table->string('domicilio_social',100)->nullable();
            $table->string('domicilio_suministro',100)->nullable();
            $table->string('cups',100)->nullable();
            $table->string('cif',100)->nullable();
            $table->string('empresa_distribuidora',100)->nullable();
            $table->string('empresa_comercializadora',100)->nullable();
            $table->string('persona_contacto',100)->nullable();
            $table->string('tarifa',100)->nullable();
            $table->string('avatar',100)->nullable(); //Campo que almacenará la ruta de la imagen perfil del usuario.
            $table->integer('user_id')->unsigned(); // Relación con usuario mediante el id.                        
            $table->timestamps();

            // Llaves foráneas.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfils');
    }
}
