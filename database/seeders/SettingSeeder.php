<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'phone_primary' => '+2347019090013',
            'phone_secondary' => '+2347086113160',
            'email' => 'tannjes03@gmail.com',
            'address' => 'Drive 2, 1st Crescent, 3rd Avenue, House 38, Prince and Princess Estate, Kaura District, Abuja',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
