<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Microcontrolador;
use Illuminate\Support\Facades\Validator;

class MicrocontroladorApiController extends Controller
{
    // Middleware para proteger con API key (se configura en routes)
    public function connect(Request $request)
    {
        $v = Validator::make($request->all(), [
            'serial' => 'nullable|string',
            'vendor_id' => 'nullable|string',
            'product_id' => 'nullable|string',
            'port' => 'required|string',
            'modelo' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        // Si hay serial usamos como clave, si no, usamos port+vendor+product para identificar
        $query = [];
        if (!empty($data['serial'])) {
            $query['serial'] = $data['serial'];
        } else {
            $query['port'] = $data['port'];
            if (!empty($data['vendor_id'])) $query['vendor_id'] = $data['vendor_id'];
            if (!empty($data['product_id'])) $query['product_id'] = $data['product_id'];
        }

        $mc = Microcontrolador::firstOrNew($query);

        $mc->serial = $data['serial'] ?? $mc->serial;
        $mc->vendor_id = $data['vendor_id'] ?? $mc->vendor_id;
        $mc->product_id = $data['product_id'] ?? $mc->product_id;
        $mc->port = $data['port'];
        $mc->modelo = $data['modelo'] ?? $mc->modelo;
        $now = isset($data['timestamp']) ? \Carbon\Carbon::parse($data['timestamp']) : now();

        if (!$mc->primera_conexion_at) $mc->primera_conexion_at = $now;
        $mc->ultima_conexion_at = $now;
        $mc->conectado = true;
        $mc->save();

        return response()->json(['success' => true, 'microcontrolador' => $mc], 200);
    }

    public function disconnect(Request $request)
    {
        $v = Validator::make($request->all(), [
            'serial' => 'nullable|string',
            'port' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        $mc = null;
        if (!empty($data['serial'])) {
            $mc = Microcontrolador::where('serial', $data['serial'])->first();
        } elseif (!empty($data['port'])) {
            $mc = Microcontrolador::where('port', $data['port'])->first();
        }

        if ($mc) {
            $now = isset($data['timestamp']) ? \Carbon\Carbon::parse($data['timestamp']) : now();
            $mc->ultima_conexion_at = $now;
            $mc->conectado = false;
            $mc->save();

            return response()->json(['success' => true, 'microcontrolador' => $mc], 200);
        }

        return response()->json(['success' => false, 'message' => 'No se encontró el microcontrolador.'], 404);
    }

    // listar microcontroladores (para la vista)
    public function list(Request $request)
    {
        $list = Microcontrolador::orderBy('ultima_conexion_at', 'desc')->get();
        return response()->json(['data' => $list], 200);
    }

    // opcional: eliminar (solo admin, verifica en rutas)
    public function destroy(Request $request, $id)
    {
        $mc = Microcontrolador::find($id);
        if (!$mc) return response()->json(['message' => 'No encontrado'], 404);
        $mc->delete();
        return response()->json(['success' => true]);
    }
}

