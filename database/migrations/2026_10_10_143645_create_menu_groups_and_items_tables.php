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
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('codigoUsuarioCriacao');
            $table->unsignedBigInteger('codigoUsuarioAlteracao');                
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_group_id')->constrained('menu_groups')->cascadeOnDelete();
            // Suporte a submenus (auto-relacionamento)
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            
            $table->string('label');
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->default('fa-circle');
            $table->string('active_pattern')->nullable(); // Ex: 'admin.projetos.*'
            $table->string('can_permission')->nullable(); // Gate/Permission
            $table->boolean('is_guest_only')->default(false);
            $table->boolean('is_external')->default(false);
            $table->string('target')->nullable(); // Ex: '_blank'
            $table->string('rel')->nullable();
            
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('codigoUsuarioCriacao');
            $table->unsignedBigInteger('codigoUsuarioAlteracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_groups');
    }
};
