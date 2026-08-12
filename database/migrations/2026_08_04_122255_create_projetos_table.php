<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {            
            $table->id();
            $table->integer('codigoPessoa');
            $table->string('codigoCurso');
            $table->string('tituloProjeto');
            $table->text('descricaoProjeto');
            $table->string('periodoProjeto');
            $table->string('linhaPesquisaProjeto');
            $table->char('statusExternoProjeto', 1)->default('N');            
            $table->string('tipoBolsaProjeto');
            $table->string('bolsaProjeto')->nullable();
            $table->date('dataInicioProjeto');
            $table->date('dataTerminoProjeto');
            $table->text('informacoesProjeto');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('codigoPessoaCriacao');
            $table->integer('codigoPessoaAlteracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
