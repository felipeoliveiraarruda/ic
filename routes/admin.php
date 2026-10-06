<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () 
{
    Route::livewire('dashboard', 'admin::index')->name('dashboard');

    /* Rotas Projetos */
    Route::livewire('projetos',                 'admin::projetos.index')->name('projetos');
    Route::livewire('projetos/create',          'admin::projetos.create')->name('projetos.create');
    Route::livewire('projetos/{projeto}/edit',  'admin::projetos.edit')->name('projetos.edit');
    Route::livewire('projetos/{projeto}/show',  'admin::projetos.show')->name('projetos.show');
});