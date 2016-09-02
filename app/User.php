<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getUserByAccount($account){
        return User::where('account', $account)->firstOrFail();
    }

    public function setCashById($id, $cash){
        User
            ::where('id', $id)
            ->increment('cash', $cash);
    }

    public function setCashByAccount($account, $cash){
        User
            ::where('account', $account)
            ->increment('cash', $cash);
    }
}