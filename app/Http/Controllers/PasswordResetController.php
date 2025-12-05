<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;   // <<-- asegúrate de esto
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


use App\Mail\ResetPasswordMail;
use App\Models\User;

class PasswordResetController extends Controller
{
    // Mostrar formulario "¿Olvidaste tu contraseña?"
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email'); // lo creamos abajo
    }

    // Enviar correo con token
    public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'correo' => 'required|email'
    ]);

    $correo = $request->input('correo');

    $user = User::where('correo', $correo)->first();
    if (! $user) {
        return back()->withErrors(['correo' => 'No existe un usuario con ese correo.']);
    }

    $token = Str::random(64);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $correo],
        ['email' => $correo, 'token' => Hash::make($token), 'created_at' => Carbon::now()]
    );

    Mail::to($correo)->send(new ResetPasswordMail($user, $token));

    // <-- en vez de back(), redirigimos a una vista "enviado"
    return redirect()->route('password.sent');
}


    // Mostrar formulario de reset (token en query)
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email'); // aquí pasamos el campo correo del usuario
        return view('auth.passwords.reset', compact('token','email'));
    }

    // Procesar el reset
    public function reset(Request $request)
    {

        $request->validate([
    'token' => 'required',
    'email' => 'required|email',
    'password' => [
        'required',
        'string',
        'min:6',                     // mínimo 6 caracteres
        'confirmed',                 // debe existir password_confirmation y coincidir
        'regex:/^(?=.*\d).+$/'       // al menos 1 dígito
    ],
], [
    'password.required' => 'La contraseña es obligatoria.',
    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    'password.confirmed' => 'Las contraseñas no coinciden.',
    'password.regex' => 'La contraseña debe contener al menos un número.',
    'email.required' => 'El correo es obligatorio.',
    'email.email' => 'El correo debe ser una dirección válida.'
]);

        $email = $request->input('email');
        $token = $request->input('token');

        // Recuperar registro guardado en password_resets
        $record = DB::table('password_resets')->where('email', $email)->first();
        if (! $record) {
            return back()->withErrors(['email' => 'Token inválido o caducado.'])->withInput();
        }

        // Comprobar token: lo guardamos como Hash::make(token). Verificamos con Hash::check
        if (! Hash::check($token, $record->token)) {
            return back()->withErrors(['token' => 'Token inválido.'])->withInput();
        }

        // Verificar tiempo de validez (ej. 60 min)
        $created = Carbon::parse($record->created_at);
        if ($created->addMinutes(60)->isPast()) {
            return back()->withErrors(['token' => 'El token ha expirado. Solicita uno nuevo.'])->withInput();
        }

        // Buscar usuario por correo (campo 'correo')
        $user = User::where('correo', $email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.'])->withInput();
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Borrar token usado
        DB::table('password_resets')->where('email', $email)->delete();

        // Opcional: iniciar sesión automáticamente o redirigir a login
        return redirect()->route('login')->with('status', 'Contraseña actualizada. Inicia sesión con tu nueva contraseña.');

    }
}
