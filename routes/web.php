<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

/*Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

/*Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['web'])->group(function () {
    Route::view('/portal-ui-demo-minimal', 'portal-ui::examples.minimal-showcase')->name('portal-ui.demo.minimal');
    Route::view('/portal-ui-demo', 'portal-ui::examples.simple-showcase')->name('portal-ui.demo');
    Route::view('/portal-ui-demo-crud', 'portal-ui::examples.admin-crud-showcase')->name('portal-ui.demo.crud');
    Route::view('/portal-ui-demo-guest', 'portal-ui::examples.guest-showcase')->name('portal-ui.demo.guest');
});*/

require __DIR__.'/auth.php';
