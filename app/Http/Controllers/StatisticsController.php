<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Redis;
use App\User;
use App\Game;
use App\Round;
use App\Settings;
use App\BetDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;

class StatisticsController extends Controller
{

    public function index()
    {
        $view = view('statistics');
        $view->msg = session()->pull('msg', 'No message!');

        return $view;
    }
}
