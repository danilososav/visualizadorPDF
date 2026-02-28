<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return file_get_contents(public_path('index.html'));
});


use App\Http\Controllers\Api\FacturaController;

Route::get('/api/facturas/{id}/descargar-pdf', [FacturaController::class, 'generarPdf']);

Route::get('/api/facturas/{id}/descargar-pdf', [FacturaController::class, 'descargarPdf']);

use App\Models\Empresa;

Route::get('/agencias', [FacturaController::class, 'agencias']);

Route::get('/api/facturas/{id}', [FacturaController::class, 'generarPdf']);
