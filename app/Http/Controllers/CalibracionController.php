<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calibracion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\EventLogger;

class CalibracionController extends Controller
{
    // Mostrar la UI
    public function index(Request $request)
    {
        // cantidad por defecto (puedes cambiar)
        $cantidad = session('cantidad_servos', 6);

        // historial de la sesión actual
        $sessionId = $request->session()->getId();
        $historial = Calibracion::where('session_id',$sessionId)
                    ->orderBy('created_at','desc')
                    ->get();

        return view('calibracion.index', compact('cantidad','historial'));
    }

    // Cambiar cantidad de servos (opcional via POST)
    public function setCantidad(Request $request)
    {
        $request->validate(['cantidad' => 'required|integer|min:1|max:255']);
        session(['cantidad_servos' => (int)$request->input('cantidad')]);
        return redirect()->route('calibracion.index');
    }

    // Guardar una calibración (llamada AJAX idealmente)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'servo_num' => 'required|integer|min:1',
            'angulo'    => 'required|integer',
        ], [
            'servo_num.required' => 'Selecciona el servomotor.',
            'angulo.required' => 'Ingresa un ángulo.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $servo = (int)$request->input('servo_num');
        $angulo = (int)$request->input('angulo');

        // validar que servo esté dentro del rango actual
        $cantidad = session('cantidad_servos', 6);
        if ($servo < 1 || $servo > $cantidad) {
            return response()->json(['errors' => ['servo_num' => ['Número de servo fuera de rango.']]], 422);
        }

        // TODO: Aquí puedes poner la llamada real a la librería serial para enviar el comando.
        // Por ahora simulamos que la calibración se hizo correctamente.

        $cal = Calibracion::create([
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'servo_num' => $servo,
            'angulo' => $angulo,
            'nota' => $request->input('nota'),
        ]);
        EventLogger::log('calibracion.create', 'calibracion', $cal->id, 'Se registró calibración', ['angles' => $data['angles'] ?? null]);

        return response()->json([
            'success' => true,
            'calibracion' => $cal
        ]);
    }
}