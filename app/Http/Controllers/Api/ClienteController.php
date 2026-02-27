<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $request->query('empresa');

        $query = Cliente::query();

        if ($empresa) {
            $query->whereHas('empresa', function ($q) use ($empresa) {
                $q->where('nombre', 'ilike', "%$empresa%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }
}
