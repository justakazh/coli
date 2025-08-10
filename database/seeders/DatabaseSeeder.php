<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\User::create([
            'name' => env("AUTH_NAME"),
            'email' => env("AUTH_EMAIL"),
            'password' => bcrypt(env('AUTH_PASSWORD')),
        ]);

        \App\Models\Terminal::create([
            'state' => 'stopped',
            'pid' => null,
        ]);

    }
}
