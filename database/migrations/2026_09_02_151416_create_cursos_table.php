<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codigoProjeto')->constrained('projetos')->onDelete('cascade');
            $table->char('codigoCurso', 10);
            $table->unsignedBigInteger('codigoPessoaCriacao');
            $table->unsignedBigInteger('codigoPessoaAlteracao');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};