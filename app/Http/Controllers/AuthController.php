<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function mostrarFormularioLogin()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required','email'],
            'password' => ['required','string'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Intentar autenticar usando 'correo' en vez de 'email'
        if (Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password']], $request->filled('remember'))) {
            // Regenera sesión
            $request->session()->regenerate();

            // Redirigir al intended o a lista de usuarios
            return redirect()->intended(route('usuarios.index'));
        }

        return back()->withErrors([
            'correo' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
