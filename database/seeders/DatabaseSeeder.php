<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear usuarios de prueba directamente en este seeder
        User::create([
            'name' => 'Admin General',
            'username' => 'admin',
            'email' => 'admin@ula.edu.ve',
            'password' => Hash::make('password'),
            'role' => 'administrador',
        ]);

        User::create([
            'name' => 'Analista 1',
            'username' => 'analista1',
            'email' => 'analista@ula.edu.ve',
            'password' => Hash::make('password'),
            'role' => 'analista',
        ]);

        User::create([
            'name' => 'Supervisor 1',
            'username' => 'supervisor1',
            'email' => 'supervisor@ula.edu.ve',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        User::create([
            'name' => 'Usuario 1',
            'username' => 'usuario1',
            'email' => 'usuario1@ula.edu.ve',
            'password' => Hash::make('password'),
            'role' => 'usuario',
        ]);
    }
}