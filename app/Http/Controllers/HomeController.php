<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Game;
use App\Round;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    const STATE_RUNNING = 0;
    const STATE_CLOSING = 1;
    const STATE_CLOSED = 2;

    public function index()
    {
        return view('index');
    }

    public function setCash($cash)
    {
        $user = User::where('account', Auth::user()->account)->firstOrFail();
        $user->cash = $cash;

        $user->save();

        return Redirect::intended('/');
    }

    public function getRunningGame()
    {
        $today = Date('Ymd');
        $game = Game
            ::where('no', 'like', "$today%")
            ->orderBy('no', 'desc')->first();

        $round = Round
            ::where('games_no', $game->no)
            ->orderBy('round', 'asc')->get();

        $game->round = $round;

        $game->now = Date('Y-m-d H:i:s');
        $game->round_interval = config('gameset.ROUND_INTERVAL');
        $game->game_interval = config('gameset.GAME_INTERVAL');
        return json_encode($game);
    }
}
