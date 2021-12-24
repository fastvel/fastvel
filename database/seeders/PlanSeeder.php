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
            for ($i=1; $i<=3; $i++) {
                DB::table('device_plans')->insert([
                    'duration' => $i,
                    'region_id' => $regionId,
                    'provider' => 'aliyun',
                    'price' => 58 * $i
                ]);
            }
        }
    }
}
