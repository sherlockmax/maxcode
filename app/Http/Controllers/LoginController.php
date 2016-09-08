<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\User;
use \Hash;


class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->flashExcept('password');
        $validator = Validator::make($request->all(), User::$login_rules, User::$messages);

        if (!$validator->fails()) {
            $attempt = Auth::attempt([
                'account' => $request->account,
                'password' => $request->password
            ]);

            if (!$attempt) {
                $validator->after(function($validator) {
                    $validator->errors()->add('field', '帳號或密碼不正確，請確認後再試一次。');
                });
            }
        }

        if($validator->fails()){
            return Redirect::to('/login')
                ->withErrors($validator)
                ->withInput();
        }else{
            return Redirect::intended('/');
        }
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    public function signUpPage(){
        return view('signup');
    }

    public function signUp(Request $request)
    {
        $request->flashExcept('password', 'password_check');
        $validator = Validator::make($request->all(), User::$rules, User::$messages);

        if($validator->fails()){

            return Redirect::to('/signup')
                ->withErrors($validator)
                ->withInput();
        }else{
            $user = new User;
            $user->account = $request->account;
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->cash = 1050226;
            $user->insert();

            return Redirect::to('/login')
                ->withInput();
        }


    }
}
