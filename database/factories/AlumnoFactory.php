<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class AlumnoFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'cedula' => fake()->numerify('1######'),
            'grado' => fake()->numberBetween(6, 11),
            'estado_prueba' => fake()->randomElement(['aprobado', 'reprobado']),
            'is_active' => fake()->numberBetween(0,1),
        ];
    }

}

