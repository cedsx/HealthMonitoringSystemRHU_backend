<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Rhu;

class RhuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rhu::create([
            'name' => 'RHU I',
            'username' => 'rhu1SIB',
            'email' => 'lenlencalderon12@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        Rhu::create([
            'name' => 'RHU II',
            'username' => 'rhu2SIB',
            'email' => 'rhu2@example.com',
            'password' => Hash::make('password123'),
        ]);

        Rhu::create([
            'name' => 'RHU III',
            'username' => 'rhu3SIB',
            'email' => 'rhu3@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
