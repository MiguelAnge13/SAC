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

use App\Http\Controllers\LibreriaController;

Route::middleware('auth')->group(function () {
    Route::get('/librerias', [LibreriaController::class, 'index'])->name('librerias.index');
    Route::post('/librerias', [LibreriaController::class, 'store'])->name('librerias.store');

    // <-- rutas estáticas / especiales primero
    Route::post('/librerias/import', [LibreriaController::class, 'importCsv'])->name('librerias.import');
    Route::get('/librerias/export', [LibreriaController::class, 'exportCsv'])->name('librerias.export');
    Route::get('/api/librerias', [LibreriaController::class, 'apiList'])->name('librerias.api');

    // ruta con parámetro al final (así no "secuestra" export)
    Route::get('/librerias/{id}', [LibreriaController::class, 'show'])->name('librerias.show');
    Route::put('/librerias/{id}', [LibreriaController::class, 'update'])->name('librerias.update');
    Route::delete('/librerias/{id}', [LibreriaController::class, 'destroy'])->name('librerias.destroy');
});


use App\Http\Controllers\InicioController;

Route::middleware('auth')->group(function () {
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
    Route::post('/inicio/foto', [InicioController::class, 'updateFoto'])->name('inicio.foto');
});

use App\Http\Controllers\PasswordResetController;

// Mostrar formulario solicitud
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
// Procesar envío del correo (ahora redirige a "sent")
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
// Página informativa después de enviar el correo
Route::get('/forgot-password/enviado', function() {
    return view('auth.passwords.sent');
})->name('password.sent');

// Formulario de reset (recibe token)
Route::get('/password/reset/form', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
// Procesar reset
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

use App\Http\Controllers\CalibracionController;

Route::middleware('auth')->group(function(){
    Route::get('/calibracion', [CalibracionController::class,'index'])->name('calibracion.index');
    Route::post('/calibracion/cantidad', [CalibracionController::class,'setCantidad'])->name('calibracion.setCantidad');
    Route::post('/calibracion', [CalibracionController::class,'store'])->name('calibracion.store');
});

use App\Http\Controllers\MicrocontroladorController;

Route::middleware('auth')->group(function () {
    Route::get('/microcontroladores', [MicrocontroladorController::class, 'index'])->name('microcontroladores.index');
    // simulación (solo para pruebas, visible en UI)
    Route::post('/microcontroladores/simular', [MicrocontroladorController::class, 'simulateConnect'])->name('microcontroladores.simular');
});

Route::middleware('auth')->group(function () {
    Route::get('/historial', [\App\Http\Controllers\HistorialController::class, 'index'])->name('historial.index');
});
