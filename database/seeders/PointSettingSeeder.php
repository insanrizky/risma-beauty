<?php

namespace Database\Seeders;

use App\Models\PointSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PointSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PointSetting::insert([
            [
                'type' => 'AGENT',
                'amount' => '3000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'RESELLER',
                'amount' => '1000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
