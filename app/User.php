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
        'password',
        'remember_token',
    ];

    public static $messages = array(
        'required'      => '所有欄位皆為必填，請確實填寫',
        'same'          => '兩次輸入的密碼不相同',
        'account.max'   => '帳號至多只能:max字元',
        'password.max'  => '密碼至多只能:max字元',
        'account.min'   => '帳號至少需要:min字元',
        'name.min'      => '暱稱至多需要:min字元',
        'password.min'  => '密碼至多需要:min字元',
        'unique'        => '該帳號已被使用',
    );

    public static $rules = array(
        'account'           => 'required|min:3|max:10|unique:users',
        'name'              => 'required|min:3|max:10',
        'password'          => 'required|min:6',
        'password_check'    => 'required|same:password'
    );

    public static $login_rules = array(
        'account'           => 'required|max:10',
        'password'          => 'required',
    );

    public function getUserByAccount($account)
    {
        return User::where('account', $account)->firstOrFail();
    }

    public function setCashById($id, $cash)
    {
        User
            ::where('id', $id)
            ->increment('cash', $cash);
    }

    public function setCashByAccount($account, $cash)
    {
        User
            ::where('account', $account)
            ->increment('cash', $cash);
    }

    public function insert(){
        $this->save();
    }
}