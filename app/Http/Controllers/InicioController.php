<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class InicioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar pantalla de inicio
    public function index()
    {
        $usuario = Auth::user();
        return view('inicio', compact('usuario'));
    }

    // Actualizar foto de perfil
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:5120', // max 5MB
        ], [
            'foto.required' => 'Selecciona una imagen.',
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.max' => 'La imagen no puede superar los 5 MB.'
        ]);

        $usuario = Auth::user();

        // eliminar archivo anterior si existe
        if ($usuario->foto) {
            Storage::disk('public')->delete($usuario->foto);
        }

        $path = $request->file('foto')->store('perfiles', 'public'); // storage/app/public/perfiles/xxx.jpg

        // Guardar ruta en DB (campo "foto")
        $usuario->foto = $path;
        $usuario->save();

        return back()->with('success', 'Foto de perfil actualizada.');
    }
}
