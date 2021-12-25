<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $regions = Region::all()->pluck('id');
        foreach ($regions as $regionId) {

                DB::table('device_plans')->insert([
                    'duration' => 1,
                    'region_id' => $regionId,
                    'provider' => 'aliyun',
                    'price' => 58
                ]);
            DB::table('device_plans')->insert([
                'duration' => 1,
                'region_id' => $regionId,
                'provider' => 'aws',
                'price' => 58
            ]);
        }
    }
}
