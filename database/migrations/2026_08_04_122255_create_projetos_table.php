<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigoPessoaResponsavel');
            $table->string('tituloProjeto');
            $table->text('descricaoProjeto')->nullable();
            $table->string('linhaPesquisaProjeto')->nullable();
            $table->string('statusExternoProjeto')->nullable();
            $table->string('tipoBolsaProjeto')->nullable();
            $table->string('bolsaProjeto')->nullable();
            $table->integer('numeroVagaProjeto')->default(1);
            $table->date('dataInicioProjeto');
            $table->date('dataTerminoProjeto');
            $table->text('informacoesProjeto')->nullable();
            $table->text('preRequisitosProjeto')->nullable();            
            $table->unsignedBigInteger('codigoPessoaCriacao');
            $table->unsignedBigInteger('codigoPessoaAlteracao');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};