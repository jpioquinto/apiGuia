<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::get('/pasos', [\App\Http\Controllers\API\LayoutController::class, 'index'])->name('layout.index');
    Route::get('/introduccion/{id}', [\App\Http\Controllers\API\ProyectoController::class, 'index'])->where('id', '[0-9]+')->name('project.index');
    Route::post('/save', [\App\Http\Controllers\API\ProyectoController::class, 'save']);

    Route::get('/antecedente/{id}/{idDiagnostico}',[\App\Http\Controllers\API\AntecedenteController::class, 'index'])->where(['id'=>'[0-9]+', 'idDiagnostico'=>'[0-9]+'])
    ->name('antecedent.index');
    Route::get('/situacion/{id}/{idProyecto}', [\App\Http\Controllers\API\SituacionController::class, 'index'])
    ->where(['id'=>'[0-9]+', 'idProyecto'=>'[0-9]+'])
    ->name('situacion.index');
    Route::get('/desarrollo/{proyecto}', [\App\Http\Controllers\API\DesarrolloController::class, 'index'])
    ->where(['proyecto'=>'[0-9]+'])
    ->name('desarrollo.index');
    Route::get('/componentes/{vertiente}/{idProyecto}', [\App\Http\Controllers\API\ComponenteController::class, 'index'])
    ->whereNumber('idProyecto')
    ->name('componente.index');
    Route::get('/subcomponente/{id}/{idProyecto}', [\App\Http\Controllers\API\CatalogoController::class, 'index'])
    ->where(['id'=>'[0-9]+', 'idProyecto'=>'[0-9]+'])
    ->name('catalogo.index');
    Route::get('/subactividades/{id}', [\App\Http\Controllers\API\CatalogoController::class, 'obtenerSubActividades'])
    ->where(['id'=>'[0-9]+'])
    ->name('catalogo.subactividad');
    Route::get('/entregables/{id}', [\App\Http\Controllers\API\CatalogoController::class, 'obtenerEntregables'])
    ->where(['id'=>'[0-9]+'])
    ->name('catalogo.entregables');
    Route::get('/unidades', [\App\Http\Controllers\API\CatalogoController::class, 'obtenerUnidades'])
    ->name('catalogo.unidades');
    Route::get('/municipios/{idProyecto}', [\App\Http\Controllers\API\CatalogoController::class, 'obtenerMunicipios'])
    ->where(['idProyecto'=>'[0-9]+'])
    ->name('catalogo.municipios');
    Route::get('/fiscalizacion/{proyecto}', [\App\Http\Controllers\API\FiscalizacionController::class, 'index'])
    ->where(['proyecto'=>'[0-9]+'])
    ->name('fiscalizacion.index');
    Route::get('/oficina/{idOficina}/{idDiagnostico}', [\App\Http\Controllers\API\OficinaController::class, 'index'])
    ->where(['idOficina'=>'[0-9]+', 'idDiagnostico'=>'[0-9]+'])
    ->name('oficina.index');
    Route::get('/oficinas/{proyecto}', [\App\Http\Controllers\API\OficinaController::class, 'listado'])
    ->where(['proyecto'=>'[0-9]+'])
    ->name('oficina.listado');
    Route::get('/pdf/{proyecto}', [\App\Http\Controllers\API\PdfController::class, 'index'])
    ->where(['proyecto'=>'[0-9]+'])
    ->name('pdf.index');

    Route::post('/save', [\App\Http\Controllers\API\ProyectoController::class, 'save']);
});
