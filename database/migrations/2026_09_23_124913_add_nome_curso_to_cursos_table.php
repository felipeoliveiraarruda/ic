<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void 
    {
        Schema::table('cursos', function (Blueprint $table) 
        {
            $table->string('nomeCurso')->nullable()->after('codigoCurso');
        });
    }

    public function down(): void 
    {
        Schema::table('cursos', function (Blueprint $table) 
        {
            $table->dropColumn('nomeCurso');
        });
    }
};
