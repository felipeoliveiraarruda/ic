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
        Schema::create('interesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('codigoProjeto')->constrained('projetos')->onDelete('cascade');
            $table->string('nomeInteresse');
            $table->string('emailInteresse');
            $table->boolean('notificacoesInteresse')->default(true);        
            $table->timestamps();
            $table->softDeletes();

            // Evita que o mesmo e-mail cadastre interesse duas vezes no mesmo projeto
            $table->unique(['codigoProjeto', 'emailInteresse']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interesses');
    }
};
