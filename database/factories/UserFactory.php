<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
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
        $username = $this->faker->unique()->userName();

        return [
            'username' => $username,
            'email' => $username . '@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'foto_profil' => null,
            'tanggal_lahir' => $this->faker->date(),
            'nomor_hp' => "081234567890",
            'jenis_kelamin' => $this->faker->randomElement(['laki-laki', 'perempuan']),
            'role' => 'customer',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
