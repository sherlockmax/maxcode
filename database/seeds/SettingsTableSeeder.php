<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
        DB::table('settings')->insert([
            'key' => 'TIME_OF_ROUND',
            'value' => '60',
            'describe' => '每回合可下注時間（秒）'
        ]);

        DB::table('settings')->insert([
            'key' => 'ROUND_PER_GAME',
            'value' => '3',
            'describe' => '每期遊戲回合數'
        ]);

        DB::table('settings')->insert([
            'key' => 'GAME_INTERVAL',
            'value' => '10',
            'describe' => '每期遊戲結束後開始下一期的間隔'
        ]);

        DB::table('settings')->insert([
            'key' => 'ROUND_INTERVAL',
            'value' => '10',
            'describe' => '每回合結束後開始下一回合的間隔'
        ]);

        DB::table('settings')->insert([
            'key' => 'CODE_RANGE_MIN',
            'value' => '1',
            'describe' => '終極密碼範圍（最小值）'
        ]);

        DB::table('settings')->insert([
            'key' => 'CODE_RANGE_MAX',
            'value' => '40',
            'describe' => '終極密碼範圍（最大值）'
        ]);

        DB::table('settings')->insert([
            'key' => 'STANDARD_ODDS',
            'value' => '0.92',
            'describe' => '標準賠率'
        ]);
    }
}
