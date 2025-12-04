<?php

use App\Http\Controllers\AuthController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarFormularioLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/usuarios', [App\Http\Controllers\UserController::class, 'index'])
    ->middleware('auth')
    ->name('usuarios.index');

use App\Http\Controllers\CodigoController;

Route::middleware('auth')->group(function () {
    Route::get('/codigos', [CodigoController::class, 'index'])->name('codigos.index');
    Route::post('/codigos', [CodigoController::class, 'store'])->name('codigos.store');
    Route::get('/codigos/{id}', [CodigoController::class, 'show'])->name('codigos.show'); // devuelve JSON
    Route::put('/codigos/{id}', [CodigoController::class, 'update'])->name('codigos.update');
    Route::delete('/codigos/{id}', [CodigoController::class, 'destroy'])->name('codigos.destroy');
});

use App\Http\Controllers\ProyectoController;

Route::middleware('auth')->group(function () {
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show'); // JSON
    Route::put('/proyectos/{id}', [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');
    Route::get('/proyectos/{id}/pdf', [ProyectoController::class, 'descargarPdf'])->name('proyectos.pdf');
});
