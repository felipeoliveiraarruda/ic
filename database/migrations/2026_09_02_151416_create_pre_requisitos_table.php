<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_requisitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codigoProjeto')->constrained('projetos')->onDelete('cascade');
            $table->unsignedBigInteger('codigoCurso');
            $table->string('periodoRequisito');
            
            $table->unsignedBigInteger('codigoPessoaCriacao')->nullable();
            $table->unsignedBigInteger('codigoPessoaAlteracao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_requisitos');
    }
};