<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->insert([
            [
                'name' => 'BCA',
                'code' => '014',
            ],
            [
                'name' => 'BNI',
                'code' => '009',
            ],
            [
                'name' => 'BRI',
                'code' => '427',
            ],
            [
                'name' => 'MANDIRI',
                'code' => '008',
            ],
            [
                'name' => 'JENIUS / BTPN',
                'code' => '213',
            ],
        ]);
    }
}
