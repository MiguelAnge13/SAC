<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MicrocontroladorApiController;

// Protección simple: middleware que valide header X-API-KEY (ver más abajo)
Route::post('/microcontroladores/connect', [MicrocontroladorApiController::class, 'connect'])->middleware('api.key');
Route::post('/microcontroladores/disconnect', [MicrocontroladorApiController::class, 'disconnect'])->middleware('api.key');

// listar (puedes permitir sin key si quieres)
Route::get('/microcontroladores', [MicrocontroladorApiController::class, 'list']);
Route::delete('/microcontroladores/{id}', [MicrocontroladorApiController::class, 'destroy'])->middleware('auth:api','api.key.admin');
