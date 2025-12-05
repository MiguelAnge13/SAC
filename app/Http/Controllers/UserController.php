<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\EventLogger;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    // Mostrar tabla de usuarios
    public function index() {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    // Registrar un nuevo usuario
    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'division' => 'nullable',
            'correo' => 'required|email|unique:users,correo',
            'password' => 'required|min:6',
            'tipo' => 'required|in:administrador,estudiante'
        ]);

        // Solo administradores pueden crear usuarios
        $usuarioActual = Auth::user();

        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error', 'No tienes permiso para agregar usuarios.');
        }

        User::create([
            'nombre' => $request->nombre,
            'division' => $request->division,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'tipo' => $request->tipo
        ]);

        return back()->with('success', 'Usuario registrado correctamente.');
    }

    // Actualizar usuario
    public function update(Request $request, $id) {
        $usuarioActual = Auth::user();

        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error', 'No tienes permiso para agregar usuarios.');
        }

        $usuario = User::findOrFail($id);

        $usuario->update([
            'nombre' => $request->nombre,
            'division' => $request->division,
            'correo' => $request->correo
        ]);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy($id) {
        $usuarioActual = Auth::user();

        if (! ($usuarioActual instanceof User) || ! $usuarioActual->esAdministrador()) {
            return back()->with('error', 'No tienes permiso para agregar usuarios.');
        }

        $usuario = User::findOrFail($id);

        // Nunca eliminar administradores
        if ($usuario->esAdministrador()) {
            return back()->with('error', 'No es posible eliminar administradores.');
        }

        $usuario->delete();

        return back()->with('success','Usuario eliminado correctamente.');
    }
}
