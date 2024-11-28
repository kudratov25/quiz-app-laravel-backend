<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\call;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'qudratov25@mail.ru',
            'password' => Hash::make('password')
        ]);

        $this->call([
            CategorySeeder::class,
            QuizSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class
        ]);
    }
}
