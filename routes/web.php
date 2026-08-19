<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

//Route::get('/',             [HomeController::class, 'index'])->name('home');
//Route::get('/{id}/show',    [HomeController::class, 'show'])->name('show');

Route::livewire('/',          'pages::index')->name('home');
Route::livewire('/{id}/show', 'pages::visualizar')->name('show');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () 
{
    Route::livewire('dashboard', 'pages::admin.dashboard')->name('dashboard');
    Route::livewire('projetos',  'pages::projetos')->name('projetos');
    Route::livewire('projetos/create',  'pages::projetos.create')->name('projetos.create');
    Route::livewire('projetos/{id}',  'pages::projetos.show')->name('projetos.show');
    Route::livewire('projetos/{projeto}/edit', 'pages::projetos.edit')->name('projetos.edit');
});

Route::middleware(['web'])->group(function () {
    Route::view('/portal-ui-demo-minimal', 'portal-ui::examples.minimal-showcase')->name('portal-ui.demo.minimal');
    Route::view('/portal-ui-demo', 'portal-ui::examples.simple-showcase')->name('portal-ui.demo');
    Route::view('/portal-ui-demo-crud', 'portal-ui::examples.admin-crud-showcase')->name('portal-ui.demo.crud');
    Route::view('/portal-ui-demo-guest', 'portal-ui::examples.guest-showcase')->name('portal-ui.demo.guest');
});


require __DIR__.'/auth.php';
