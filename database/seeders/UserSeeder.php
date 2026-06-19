<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => UserRoleEnum::ADMIN,
        ]);

        User::factory()->create([
            'full_name' => 'Customer 1',
            'email' => 'customer@example.com',
            'role' => UserRoleEnum::CUSTOMER,
        ]);
    }
}
