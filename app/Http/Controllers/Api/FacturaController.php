<?php

namespace App\Http\Controllers\Api;

use App\Models\Factura;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $request->query('empresa');
        $search = $request->query('q');

        $query = Factura::with(['empresa', 'cliente', 'items']);

        if ($empresa) {
            $query->whereHas('empresa', function ($q) use ($empresa) {
                $q->where('nombre', 'ilike', "%$empresa%");
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'ilike', "%$search%")
                  ->orWhereHas('cliente', function ($subQ) use ($search) {
                      $subQ->where('nombre', 'ilike', "%$search%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function show($id)
    {
        $factura = Factura::with(['empresa', 'cliente', 'items'])->find($id);

        if (!$factura) {
            return response()->json(['error' => 'Factura no encontrada'], 404);
        }

        return response()->json($factura);
    }

    public function generarPdf($id)
{
    $factura = Factura::with(['empresa', 'cliente', 'items'])->find($id);

    if (!$factura) {
        return response()->json(['error' => 'Factura no encontrada'], 404);
    }

    $datos = [
        'ruc_empresa' => $factura->empresa->ruc . '-' . $factura->empresa->dv,
        'num_timbrado' => $factura->numero_timbre ?? '17593723',
        'num_factura' => str_pad($factura->numero, 7, '0', STR_PAD_LEFT),
        'fecha_emision' => $factura->fecha_emision->format('d-m-Y'),
        'ruc_receptor' => $factura->cliente->ruc . '-' . $factura->cliente->dv,
        'codigo_cliente' => str_pad($factura->cliente->id, 15, '0', STR_PAD_LEFT),
        'nombre_cliente' => $factura->cliente->nombre,
        'condicion_venta' => $factura->desc_tipo_operacion ?? 'Crédito',
        'cuotas' => (string)($factura->tipo_operacion ?? '1'),
        'moneda' => $factura->moneda ?? 'US Dollar',
        'direccion' => $factura->cliente->direccion ?? '',
        'email' => $factura->cliente->email ?? '',
        'tipo_cambio' => (string)($factura->tasa_cambio ?? ''),
        'items' => $factura->items->map(function($item) {
            return [
                'codigo' => $item->id,
                'descripcion' => $item->descripcion,
                'unidad' => $item->unidad_medida ?? 'UNI',
                'cantidad' => number_format($item->cantidad, 2, '.', ''),
                'precio_unitario' => number_format($item->precio_unitario, 2, '.', ''),
                'descuento' => '0',
                'exentas' => '0',
                'cinco_porciento' => '0',
                'diez_porciento' => number_format($item->total_item, 2, ',', '.')
            ];
        })->all(),
        'subtotal' => number_format($factura->subtotal, 2, ',', '.'),
        'total_operacion' => number_format($factura->total, 2, ',', '.'),
        'total_guaranies' => number_format($factura->total, 2, ',', '.'),
        'iva_cinco' => '0',
        'iva_diez' => number_format($factura->iva, 2, ',', '.'),
        'total_iva' => number_format($factura->iva, 2, ',', '.')
    ];

    return view('facturas.factura', $datos);
}

}
