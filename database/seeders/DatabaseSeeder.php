<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Guest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        Role::create([
            'name' => 'admin'
        ]);

        \App\Models\User::factory()->create([
            'uniq_id' => generateUuid(),
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('P@ssw0rd'),
        ]);

        Guest::create([
            'uniq_id' => generateUuid(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'location' => 'New York',
            'email' => 'jhondoe@gmail.com',
        ]);
    }
}
