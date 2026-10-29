<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () 
{
    Route::livewire('dashboard', 'admin::index')->name('dashboard');

    /* Rotas Projetos */
    Route::livewire('projetos/dashboard',       'admin::projetos.dashboard')->name('projetos.dashboard');
    Route::livewire('projetos',                 'admin::projetos.index')->name('projetos');
    Route::livewire('projetos/create',          'admin::projetos.create')->name('projetos.create');
    Route::livewire('projetos/{projeto}/edit',  'admin::projetos.edit')->name('projetos.edit');
    Route::livewire('projetos/{projeto}/show',  'admin::projetos.show')->name('projetos.show');

    /* Rotas Menu */
    Route::livewire('menus',                'admin::menus.index')->name('menus');
    Route::livewire('menus/create',         'admin::menus.create')->name('menus.create');
    Route::livewire('menus/{menu}/edit',    'admin::menus.edit')->name('menus.edit');
    Route::livewire('menus/grupos',         'admin::menus.grupos')->name('menus.grupos');

    /* Rotas Inscrições */
    Route::livewire('inscricoes', 'admin::inscricoes.index')->name('inscricoes');
});