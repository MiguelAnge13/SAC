<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EventLogger;
use App\Models\User;

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
        'correo' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password']], $request->filled('remember'))) {
        $request->session()->regenerate();

        EventLogger::log('login', 'usuario', Auth::id(), 'Inicio de sesión', [
            'correo' => Auth::user()->correo
        ]);

        return redirect()->intended(route('inicio'));
    }

    return back()->withErrors(['correo' => 'Credenciales inválidas.'])->withInput();
}

    // Cerrar sesión
    public function logout(Request $request)
{
    $userId = Auth::id();

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    EventLogger::log('logout', 'usuario', $userId, 'Cierre de sesión');

    return redirect()->route('login');
}
}
