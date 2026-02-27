<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacturaController;
use App\Http\Controllers\Api\ClienteController;

Route::get('/agencias', function () {
    $agencias = \App\Models\Empresa::where('activo', true)
        ->pluck('nombre')
        ->unique()
        ->values();
    return response()->json($agencias);
});

Route::get('/facturas', [FacturaController::class, 'index']);
Route::get('/facturas/{id}', [FacturaController::class, 'show']);
Route::get('/facturas/{id}/descargar-pdf', [FacturaController::class, 'generarPdf']);

Route::get('/clientes', [ClienteController::class, 'index']);
Route::get('/clientes/{id}', [ClienteController::class, 'show']);
