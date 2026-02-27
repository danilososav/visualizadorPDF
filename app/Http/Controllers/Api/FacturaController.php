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
    'num_factura' => substr($factura->numero, -7),
    'fecha_emision' => $factura->fecha_emision->format('d-m-Y'),
    'ruc_receptor' => $factura->cliente->ruc . '-' . $factura->cliente->dv,
    'codigo_cliente' => $factura->cliente->id,
    'nombre_cliente' => $factura->cliente->nombre,
    'condicion_venta' => $factura->desc_tipo_operacion ?? 'Crédito',
    'cuotas' => $factura->tipo_operacion ?? '1',
    'moneda' => $factura->moneda ?? 'US Dollar',
    'direccion' => $factura->cliente->direccion ?? '',
    'email' => $factura->cliente->email ?? '',
    'tipo_cambio' => ($factura->tasa_cambio ?? 0) == intval($factura->tasa_cambio ?? 0) ? intval($factura->tasa_cambio ?? 0) : $factura->tasa_cambio,
    // NUEVOS CAMPOS DINÁMICOS
    'nombre_empresa' => $factura->empresa->nombre,
    'actividad_empresa' => 'Actividades publicitarias', // O sacarlo de una tabla si existe
    'direccion_empresa' => $factura->empresa->direccion ?? 'AVDA BRASILIA CASI GAETANO MARTINO',
    'ciudad_empresa' => 'ASUNCION (DISTRITO)',
    'email_empresa' => $factura->empresa->email ?? 'GENESIS@ATOMIK.PRO',
    'telefono_empresa' => $factura->empresa->telefono ?? '0991707465',
    'items' => $factura->items->map(function($item, $index) {
        return [
            'codigo' => $index + 1,
            'descripcion' => $item->descripcion,
            'unidad' => $item->unidad_medida ?? 'UNI',
            'cantidad' => intval($item->cantidad ?? 0),
            'precio_unitario' => number_format($item->precio_unitario ?? 0, 0, ',', '.'),
            'descuento' => '0',
            'exentas' => '0',
            'cinco_porciento' => '0',
            'diez_porciento' => number_format($item->total_item ?? 0, 2, ',', '.')
        ];
    })->all(),
    'subtotal' => (($factura->subtotal ?? 0) == intval($factura->subtotal ?? 0)) ? number_format($factura->subtotal ?? 0, 0, ',', '.') : number_format($factura->subtotal ?? 0, 2, ',', '.'),
    'total_operacion' => (($factura->subtotal ?? 0) == intval($factura->subtotal ?? 0)) ? number_format($factura->subtotal ?? 0, 0, ',', '.') : number_format($factura->subtotal ?? 0, 2, ',', '.'),
    'total_guaranies' => number_format($factura->total ?? 0, 0, ',', '.'),
    'iva_cinco' => '0',
    'iva_diez' => (($factura->iva ?? 0) == intval($factura->iva ?? 0)) ? number_format($factura->iva ?? 0, 0, ',', '.') : number_format($factura->iva ?? 0, 2, ',', '.'),
    'total_iva' => (($factura->iva ?? 0) == intval($factura->iva ?? 0)) ? number_format($factura->iva ?? 0, 0, ',', '.') : number_format($factura->iva ?? 0, 2, ',', '.')
];

return view('facturas.factura', $datos);
}

}
