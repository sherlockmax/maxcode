<?php

use Illuminate\Database\Seeder;
use \App\User;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();
        User::create([
            'account' => 'finalgod',
            'name' => 'Final God',
            'password' => Hash::make('123456'),
            'cash' => 10000,
        ]);
    }
}