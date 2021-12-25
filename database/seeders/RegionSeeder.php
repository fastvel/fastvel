<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['code' => 'cn-beijing', 'name' => '北京', 'group' => '中国'],
            ['code' => 'cn-shenzhen', 'name' => '深圳', 'group' => '中国'],
            ['code' => 'cn-guangzhou', 'name' => '广州', 'group' => '中国'],
            ['code' => 'cn-shanghai', 'name' => '上海', 'group' => '中国'],
            ['code' => 'hongkong', 'name' => '香港', 'group' => '中国'],
//            ['code' => 'cn-qingdao', 'name' => '青岛', 'group' => '中国大陆'],
//            ['code' => 'cn-hangzhou', 'name' => '杭州', 'group' => '中国大陆'],
//            ['code' => 'cn-chengdu', 'name' => '成都', 'group' => '中国大陆'],

            ['code' => 'us', 'name' => '美国', 'group' => '欧美'],
            ['code' => 'de', 'name' => '德国', 'group' => '欧美'],
            ['code' => 'fr', 'name' => '法国', 'group' => '欧美'],
            ['code' => 'gb', 'name' => '英国', 'group' => '欧美'],
            // ['code' => 'it', 'name' => '意大利(米兰)', 'group' => '欧美'],

            ['code' => 'jp', 'name' => '日本', 'group' => '亚太与中东'],
            ['code' => 'kr', 'name' => '韩国', 'group' => '亚太与中东'],
            ['code' => 'au', 'name' => '澳大利亚', 'group' => '亚太与中东'],
            ['code' => 'sg', 'name' => '新加坡', 'group' => '亚太与中东'],
            ['code' => 'in', 'name' => '印度', 'group' => '亚太与中东'],
            ['code' => 'ae', 'name' => '阿联酋', 'group' => '亚太与中东'],
        ];
        DB::table('regions')->insert($regions);
    }
}
