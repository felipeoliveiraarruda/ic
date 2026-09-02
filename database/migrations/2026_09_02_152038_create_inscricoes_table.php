<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codigoEdital')->constrained('editais')->onDelete('cascade');
            $table->string('cursoInscricao');
            $table->string('instituicaoInscricao');
            $table->string('historicoEscolarInscricao')->nullable();
            
            $table->unsignedBigInteger('codigoPessoaCriacao')->nullable();
            $table->unsignedBigInteger('codigoPessoaAlteracao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
