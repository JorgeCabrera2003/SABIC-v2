<?php

namespace Database\Factories;

use App\Models\Personal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personal>
 */
class PersonalFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente al factory.
     *
     * @var string
     */
    protected $model = Personal::class;

    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document' => fake()->unique()->numerify('########'), // Genera una cédula aleatoria
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'id_nominal_location' => fake()->randomElement(\App\Models\NominalLocation::pluck('id')->toArray()),
            'id_position' => fake()->randomElement(\App\Models\Position::pluck('id')->toArray()),
            'photo_dir' => 'fotos-personal/default.png', // Una ruta por defecto
            'status' => fake()->randomElement(['active', 'inactive', 'vacation', 'authorized', 'unauthorized']), // Según tu enum
        ];
    }
}
