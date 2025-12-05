<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Codigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\EventLogger;

class CodigoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar la vista con la lista de códigos
    public function index()
    {
        $codigos = Codigo::orderBy('titulo')->get();
        return view('codigos.index', compact('codigos'));
    }

    // Guardar nuevo código
    public function store(Request $request)
{
    $data = $request->validate([
        'titulo'   => 'nullable|string|max:255',
        'lenguaje' => 'nullable|string|max:100',
        'fecha'    => 'nullable|date',
        'codigo'   => 'nullable|string',
    ]);

    $data['user_id'] = Auth::id();

    // crear el registro en BD
    $codigo = Codigo::create($data);

    // registrar evento en historial
    EventLogger::log(
        'code.create',          // acción
        'codigo',               // entidad
        $codigo->id,            // entity_id
        'Se creó código',       // descripción legible
        [
            'titulo' => $codigo->titulo,
            'lenguaje' => $codigo->lenguaje
        ]
    );

    return redirect()->route('codigos.index')->with('success', 'Código guardado correctamente.');
}


    // Devolver JSON del código (para cargar en formulario con JS)
    public function show($id)
{
    $codigo = Codigo::findOrFail($id);
    // (opcional) no es habitual loggear solo una vista, pero puedes:
    // EventLogger::log('code.view', 'codigo', $codigo->id, 'Se visualizó código');

    return response()->json($codigo);
}


    // Actualizar código existente
    public function update(Request $request, $id)
{
    $codigo = Codigo::findOrFail($id);

    $old = $codigo->getOriginal(); // para meta -> old values

    $data = $request->validate([
        'titulo'   => 'nullable|string|max:255',
        'lenguaje' => 'nullable|string|max:100',
        'fecha'    => 'nullable|date',
        'codigo'   => 'nullable|string',
    ]);

    $codigo->update($data);

    EventLogger::log(
        'code.update',
        'codigo',
        $codigo->id,
        'Código actualizado',
        [
            'old' => $old,
            'new' => $codigo->toArray()
        ]
    );

    return redirect()->route('codigos.index')->with('success', 'Código actualizado correctamente.');
}


    // Eliminar código (solo admin)
    public function destroy($id)
{
    $codigo = Codigo::findOrFail($id);

    $snapshot = $codigo->toArray();

    $codigo->delete();

    EventLogger::log(
        'code.delete',
        'codigo',
        $id,
        'Código eliminado',
        ['data' => $snapshot]
    );

    return redirect()->route('codigos.index')->with('success', 'Código eliminado.');
}

}
