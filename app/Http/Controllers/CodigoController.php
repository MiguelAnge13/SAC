<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Codigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        // Solo administradores pueden crear? según tu petición antes: "solo admin puede eliminar", pero no restrict creado.
        // Aquí dejamos que cualquier usuario autenticado pueda crear. Si quieres que solo admin cree, descomenta la verificación.
        /*
        if (!Auth::user()->esAdministrador()) {
            return back()->with('error', 'No tienes permiso para crear códigos.');
        }
        */

        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'lenguaje' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
            'codigo' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        Codigo::create($data);

        return redirect()->route('codigos.index')->with('success', 'Código guardado correctamente.');
    }

    // Devolver JSON del código (para cargar en formulario con JS)
    public function show($id)
    {
        $codigo = Codigo::findOrFail($id);
        return response()->json($codigo);
    }

    // Actualizar código existente
    public function update(Request $request, $id)
    {
        $codigo = Codigo::findOrFail($id);

        // Opcional: verificar permisos (si sólo admin puede editar, añade la comprobación)
        // if (!Auth::user()->esAdministrador()) { ... }

        $data = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'lenguaje' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
            'codigo' => 'nullable|string',
        ]);

        $codigo->update($data);

        return redirect()->route('codigos.index')->with('success', 'Código actualizado correctamente.');
    }

    // Eliminar código (solo admin)
    public function destroy($id)
    {
        $usuario = Auth::user();

        $usuarioActual = Auth::user();
        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error', 'Solo administradores pueden eliminar códigos.');
        }

        $codigo = Codigo::findOrFail($id);
        $codigo->delete();

        return redirect()->route('codigos.index')->with('success', 'Código eliminado correctamente.');
    }
}
