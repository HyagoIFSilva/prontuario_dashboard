<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tbExame', function (Blueprint $table) {
            $table->id('idExamePK');

        
            $table->foreignId('idConsultaFK')
                  ->constrained('tbConsulta', 'idConsultaPK')
                  ->cascadeOnDelete();

            $table->foreignId('idPacienteFK')
                  ->constrained('tbPaciente', 'idPaciente')
                  ->cascadeOnDelete();

            $table->foreignId('idProntuarioFK')
                  ->constrained('tbProntuario', 'idProntuarioPK')
                  ->cascadeOnDelete();
   
            $table->string('nomeExame')->nullable(); 
            $table->string('tipoExame')->nullable();
            
     
            $table->string('descExame'); 
            
            
            $table->date('dataExame')->nullable(); 
            $table->string('statusExame')->default('SOLICITADO');

            $table->timestamps();
            $table->softDeletes();

            
            // $table->unique(['idConsultaFK', 'descExame', 'dataExame']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Método robusto para apagar a tabela
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tbExame');
        Schema::enableForeignKeyConstraints();
    }
};