<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'currency_code' => 'BDT',
            'currency_symbol' => '৳',
            'timezone' => 'Asia/Dhaka',
            'date_format' => 'd M Y',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
