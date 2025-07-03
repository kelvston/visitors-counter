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
        $this->call(SettingsSeeder::class);

//        User::create([
//            'name' => 'Admin',
//            'email' => 'admin@dukani.com',
//            'password' => bcrypt('admin123'),
//            'role' => 'admin',
//        ]);

    }
}
