<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nombre' => 'Fidel Paredes Xochihua',
            'division' => 'Ingenieria Electromecanica',
            'correo' => 'fidel@gmail.com',
            'password' => Hash::make('1234'), // contraseña: admin123
            'tipo' => 'administrador'
        ]);
    }
}
