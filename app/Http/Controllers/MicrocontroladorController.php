<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Microcontrolador;

class MicrocontroladorController extends Controller
{
    // Mostrar la vista principal (lista + estado)
    public function index()
    {
        // la vista usará fetch para obtener datos en tiempo real
        return view('microcontroladores.index');
    }

    // Simular conexión (solo para pruebas, opcional)
    public function simulateConnect(Request $request)
    {
        $data = $request->validate([
            'serial' => 'nullable|string',
            'vendor_id' => 'nullable|string',
            'product_id' => 'nullable|string',
            'port' => 'required|string',
            'modelo' => 'nullable|string',
        ]);

        // delega a la lógica central (upsert)
        $mc = Microcontrolador::firstOrNew(['serial' => $data['serial'] ?? null]);
        $mc->vendor_id   = $data['vendor_id'] ?? $mc->vendor_id;
        $mc->product_id  = $data['product_id'] ?? $mc->product_id;
        $mc->port        = $data['port'];
        $mc->modelo      = $data['modelo'] ?? $mc->modelo;
        $now = now();
        if (!$mc->primera_conexion_at) {
            $mc->primera_conexion_at = $now;
        }
        $mc->ultima_conexion_at = $now;
        $mc->conectado = true;
        $mc->save();

        return back()->with('status', 'Simulación de conexión registrada.');
    }
}
