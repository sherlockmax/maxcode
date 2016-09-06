<?php

use Illuminate\Database\Seeder;
use \App\User;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();
        User::create([
            'account' => 'max',
            'name' => 'Max',
            'password' => Hash::make('123456'),
            'cash' => 1050226,
        ]);
    }
}