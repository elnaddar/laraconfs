<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\User;
use App\Models\Venue;
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
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => '123123'
        ]);

        $venues = Venue::factory(200)->create();
        $confs = Conference::factory(10)->recycle($venues)->create();
    }
}
