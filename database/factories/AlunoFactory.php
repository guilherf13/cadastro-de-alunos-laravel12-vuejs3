<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Aluno;
use App\Enums\StatusAluno;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluno>
 */
class AlunoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'data_nascimento' => $this->faker->date('Y-m-d'),
            'turma' => 'Turma ' . $this->faker->randomLetter(),
            'telefone' => $this->faker->numerify('###########'),
            'curso' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(StatusAluno::cases()),
        ];
    }
}
