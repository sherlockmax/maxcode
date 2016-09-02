<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Game;
use App\Round;
use App\BetDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    const MESSAGE_ROUND_RUNNING = 'Round {index} will end in {sec} seconds.';
    const MESSAGE_ROUND_END = 'Round {index} will start in {sec} seconds.';
    const MESSAGE_GAME_END = 'New game will start in {sec} seconds.';

    public function index()
    {
        $view = view('index');
        $view->msg = session()->pull('msg', 'No message!');

        return $view;
    }

    public function setCash($account, $cash)
    {
        if(Auth::user()->account == 'max') {
            $user_model = new User;
            $user_model->setCashByAccount($account, $cash);

            return Redirect::intended('/');
        }else{
            return "You have not permission to do that!!";
        }
    }

    public function getGameData()
    {
        $today = Date('Ymd');
        $game_model = new Game;
        $round_model = new Round;
        $game = $game_model->getCurrentGame($today);
        $rounds = $round_model->getRoundByGameNo($game->no);

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
        $game_model = new Game;
        $game_data = $game_model->getGameByNoState($games_no, config('gameset.STATE_CLOSED'));

        if($game_data){
            $final_code = $game_data->final_code;
        }

        return $final_code;
    }

    public function playerBet(Request $request){
        $is_pass = true;
        $games_no = $request->input('games_no');
        $round_no = $request->input('round_no');
        $bet_part1 = $request->input('bet_part1');
        $num_type = $request->input('numType')[0];
        $bet_part2 = $request->input('bet_part2');
        $number = $request->input('numbers')[0];
        $odds_odd = $request->input('odds_odd');
        $odds_even = $request->input('odds_even');
        $odds_numbers = $request->input('odds_numbers');

        $user_model = new User;
        $game_model = new Game;
        $round_model = new Round;

        $user = $user_model->getUserByAccount(Auth::user()->account);
        $game = $game_model->getGameByNoState($games_no, config('gameset.STATE_RUNNING'));
        $round = $round_model->getRoundByGameNoRound($games_no, $round_no);

        if($bet_part1 + $bet_part2 > $user->cash){
            $is_pass = false;
            session()->put('msg',  "Your cash is not enough to play.");
        }

        if(is_null($num_type) && is_null($number)){
            $is_pass = false;
            session()->put('msg',  "Your need to choose one part to play at least .");
        }

        if((!is_null($num_type) && $bet_part1 <= 0) || (!is_null($number) && $bet_part2 <= 0)){
            $is_pass = false;
            session()->put('msg',  "Your need put cash to play.");
        }

        if($is_pass) {
            if ($game && $round) {

                if((!is_null($number) && $bet_part2 > 0)){
                    $bet_detail_model = new BetDetail;

                    $detail = $bet_detail_model->getByUniqueField($games_no, $user->id, $round_no, 2);

                    if(is_null($detail)) {
                        $bet_detail_model->user_id = $user->id;
                        $bet_detail_model->games_no = $games_no;
                        $bet_detail_model->round = $round_no;
                        $bet_detail_model->bet_at = time();
                        $bet_detail_model->win_cash = 0;
                        $bet_detail_model->part = 2;
                        $bet_detail_model->guess = $number;
                        $bet_detail_model->bet = $bet_part2;
                        $bet_detail_model->odds = $odds_numbers;
                        $bet_detail_model->save();

                        $user->cash = $user->cash - $bet_part2;
                        $user->save();

                        session()->put('msg', "bet success.");
                    }else{
                        session()->put('msg', "You already bet, Do not bet agian.");
                    }
                }

                if((!is_null($num_type) && $bet_part1 > 0)){
                    $bet_detail_model = new BetDetail;

                    $detail = $bet_detail_model->getByUniqueField($games_no, $user->id, $round_no, 1);

                    if(is_null($detail)) {
                        $bet_detail_model->user_id = $user->id;
                        $bet_detail_model->games_no = $games_no;
                        $bet_detail_model->round = $round_no;
                        $bet_detail_model->bet_at = time();
                        $bet_detail_model->win_cash = 0;
                        $bet_detail_model->part = 1;
                        $bet_detail_model->guess = $num_type;
                        $bet_detail_model->bet = $bet_part1;
                        $bet_detail_model->odds = $odds_odd;
                        if($num_type%2 == 0){
                            $bet_detail_model->odds = $odds_even;
                        }
                        $bet_detail_model->save();

                        $user->cash = $user->cash - $bet_part1;
                        $user->save();

                        session()->put('msg', "bet success.");
                    }else{
                        session()->put('msg', "You already bet, Do not bet agian.");
                    }
                }

            } else {
                session()->put('msg', "The game is not running, please check and try again.");
            }
        }

        return Redirect::intended('/');
    }

    public function getBetHistory(){
        $bet_detail_model = new BetDetail;
        $game_model = new Game;
        $bet_details = $bet_detail_model->getByUserId(Auth::user()->id);

        $bet_array = [];
        foreach($bet_details as $bet){
            $game = $game_model->getGameByNoState($bet->games_no, config('gameset.STATE_CLOSED'));

            if($game){
                $bet->final_code = $game->final_code;
            }else{
                $bet->final_code = '?';
            }

            $bet_array[] = $bet;
        }

        $user_model = new User;
        $user = $user_model->getUserByAccount(Auth::user()->account);
        $bet_array['cash'] = $user->cash;

        return json_encode($bet_array);
    }

    public function billingRound(){
        $bet_detail_model = new BetDetail;
        $user_model = new User;
        $round_model = new Round;
        $bet_details = $bet_detail_model->getNotFinishedByPart(1);
        foreach($bet_details as $bet){
            $round = $round_model->getRoundByGameNoRound($bet->games_no, $bet->round);
            $round_code_type = $round->round_code % 2;
            $win_cash = $bet->bet;
            if($bet->guess % 2 == $round_code_type){
                $win_cash = $win_cash + ($bet->bet * $bet->odds);
            }else{
                $win_cash = $bet->bet * -1;
            }

            $bet->win_cash = $win_cash;
            $bet->save();

            if($win_cash > 0) {
                $user_model->setCashById($bet->user_id, $bet->win_cash);
            }
        }

        return $bet_details;
    }

    public function billingGame(){
        $bet_detail_model = new BetDetail;
        $user_model = new User;
        $game_model = new Game;
        $bet_details = $bet_detail_model->getNotFinishedByPart(2);
        foreach($bet_details as $bet){
            $game = $game_model->getGameByNoState($bet->games_no, config('gameset.STATE_CLOSED'));
            $win_cash = $bet->bet;
            if($bet->guess == $game->final_code){
                $win_cash = $win_cash + ($bet->bet * $bet->odds);
            }else{
                $win_cash = $bet->bet * -1;
            }

            $bet->win_cash = $win_cash;
            $bet->save();

            if($win_cash > 0) {
                $user_model->setCashById($bet->user_id, $bet->win_cash);
            }
        }
    }
}
