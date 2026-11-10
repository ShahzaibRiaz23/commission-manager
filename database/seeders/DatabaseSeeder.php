<?php

namespace Database\Seeders;

use App\Models\User;   
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Yahan call array ke andar CommissionSettingSeeder bhi add kar dein:
        $this->call([
            RolePermissionSeeder::class,
            CommissionSettingSeeder::class, // <-- Yahan add karna hai
        ]);

        // Optional test user code...
    }
}