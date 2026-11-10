<?php

namespace Database\Seeders;

use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingSeeder extends Seeder
{
    public function run(): void
    {
        CommissionSetting::firstOrCreate(
            ['key' => 'default_va_commission'],
            ['value' => 10.00, 'description' => 'Default Virtual Assistant commission percentage']
        );

        CommissionSetting::firstOrCreate(
            ['key' => 'default_sales_commission'],
            ['value' => 5.00, 'description' => 'Default Sales Officer commission percentage']
        );
    }
}