<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Gestor Teste',
            'email' => 'gestor@test.com',
            'password' => Hash::make('password'),
            'perfil' => 'Gestor',
        ]);

        User::create([
            'name' => 'Funcionário Teste',
            'email' => 'funcionario@test.com',
            'password' => Hash::make('password'),
            'perfil' => 'Funcionario',
        ]);
    }
}
