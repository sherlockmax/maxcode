<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'cash',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $messages = array(
        'required' => '所有欄位皆為必填，請確實填寫',
        'same' => '兩次輸入的密碼不相同',
        'account.max' => '帳號至多只能:max字元',
        'password.max' => '密碼至多只能:max字元',
        'account.min' => '帳號至少需要:min字元',
        'name.min' => '暱稱至多需要:min字元',
        'password.min' => '密碼至多需要:min字元',
        'unique' => '該帳號已被使用',
    );

    public static $rules = array(
        'account' => 'required|min:3|max:10|unique:users',
        'name' => 'required|min:3|max:10',
        'password' => 'required|min:6',
        'password_check' => 'required|same:password'
    );

    public static $login_rules = array(
        'account' => 'required|max:10',
        'password' => 'required',
    );

    /**
     * 根據帳號取得使用者
     *
     * @param $account　帳號
     * @return mixed
     */
    public function getUserByAccount($account)
    {
        return User::where('account', $account)->firstOrFail();
    }

    /**
     * 根據使用者帳號增加cash金額
     *
     * @param $account　帳號
     * @param $cash
     */
    public function setCashByAccount($account, $cash)
    {
        User
            ::where('account', $account)
            ->increment('cash', $cash);
    }
}