<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Profesor;
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

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test Profesor',
            'email' => 'test1@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test Alumno',
            'email' => 'test2@example.com',
        ]);
        
        Alumno::factory()->count(50)->create();
        Profesor::factory()->count(10)->create();


        $this->call(RolesAndPermissionsSeeder::class);
        
    }
}
