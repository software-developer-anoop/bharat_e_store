<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'user_name' => 'super_admin',
                'email' => 'bharatestore@gmail.com',
                'email_verified_at'=> now(),
                'password' => Hash::make('super_admin@2025'), // Secure password
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
