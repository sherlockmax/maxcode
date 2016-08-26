<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Auth;
use App\Http\Requests;
use App\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function setCash($cash){
        $user = User::where('account', Auth::user()->account)->firstOrFail();
        $user->cash = $cash;

        $user->save();

        return Redirect::intended('/');
    }
}
