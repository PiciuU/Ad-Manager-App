<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'user_role_id' => 2,
            'name' => 'System',
            'login' => 'admin',
            'password' => Hash::make('system2023'),
            'email' => 'admin@system.com',
            'nip' => null,
            'address' => null,
            'postal_code' => null,
            'country' => 'Polska',
            'company_email' => null,
            'company_phone' => null,
            'representative' => null,
            'representative_phone' => null,
        ]);

        User::factory()->count(8)->create();

        User::factory()->count(2)->create([
            'activation_key' => md5(uniqid()),
            'activated_at' => null,
        ]);
    }
}
