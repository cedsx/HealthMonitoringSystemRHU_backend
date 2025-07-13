<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Barangay;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barangay::create([
            'rhu_id' => 1,
            'name' => 'Bagong Barrio',
            'username' => 'bagongbarrioSIB',
            'email' => 'kiptelegen82@gmail.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }
}
