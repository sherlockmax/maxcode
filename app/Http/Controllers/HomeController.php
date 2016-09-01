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
    const MESSAGE_ROUND_RUNNING = 'Round {index} will end in {sec} seconds.';
    const MESSAGE_ROUND_END = 'Round {index} will start in {sec} seconds.';
    const MESSAGE_GAME_END = 'New game will start in {sec} seconds.';

    public function index()
    {
        return view('index');
    }

    public function setCash($cash)
    {
        $userModel = new User;
        $user = $userModel->getUserByAccount(Auth::user()->account);
        $user->cash = $cash;
        $user->save();

        return Redirect::intended('/');
    }

    public function getGameData()
    {
        $today = Date('Ymd');
        $gameModel = new Game;
        $roundModel = new Round;
        $game = $gameModel->getCurrentGame($today);
        $rounds = $roundModel->getRoundByGameNo($game->no);

        $round_last = $rounds[sizeof($rounds) - 1];
        $game->round = $rounds;

        $game->timer = $round_last->end_at - time();
        $game->msg = str_replace('{index}', sizeof($rounds), self::MESSAGE_ROUND_RUNNING);

        if($game->state == 0){
            if($round_last->state == 2){
                $next_round_start_at = $round_last->end_at + config('gameset.ROUND_INTERVAL');
                $game->timer = $next_round_start_at - time();
                $game->msg = str_replace('{index}', sizeof($rounds)+1, self::MESSAGE_ROUND_END);
            }
        }

        if($game->state == 2){
            $next_game_start_at = $round_last->end_at + config('gameset.GAME_INTERVAL');
            $game->timer = $next_game_start_at - time();
            $game->msg = self::MESSAGE_GAME_END;
        }

        $game->odds = calcOdds($round_last->current_min, $round_last->current_max);

        return $game->toJson();
    }

    public function getFinalCode($games_no){
        $final_code = '?';
        $gameModel = new Game;
        $game_data = $gameModel->getGameByNoState($games_no, config('gameset.STATE_CLOSED'));

        if($game_data){
            $final_code = $game_data->final_code;
        }

        return $final_code;
    }
}
