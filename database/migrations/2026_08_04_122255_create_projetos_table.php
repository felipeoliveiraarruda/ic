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
            $table->string('nomePessoaResponsavel')->nullable();
            $table->string('tituloProjeto');
            $table->text('descricaoProjeto');
            $table->string('linhaPesquisaProjeto');
            $table->string('statusExternoProjeto');
            $table->string('tipoBolsaProjeto');
            $table->string('bolsaProjeto')->nullable();
            $table->integer('numeroVagaProjeto')->default(1);
            $table->date('dataInicioProjeto');
            $table->date('dataTerminoProjeto');
            $table->text('informacoesProjeto')->nullable();
            $table->text('preRequisitosProjeto')->nullable();            
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('codigoUsuarioCriacao');
            $table->unsignedBigInteger('codigoUsuarioAlteracao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};