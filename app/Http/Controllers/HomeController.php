<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Game;
use App\Round;
use App\Settings;
use App\BetDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;

class HomeController extends Controller
{
    const MESSAGE_ROUND_RUNNING = '第&nbsp;{index}&nbsp;回合將在&nbsp;{sec}&nbsp;秒後結束下注';
    const MESSAGE_ROUND_END = '第&nbsp;{index}&nbsp;回合將在&nbsp;{sec}&nbsp;秒後開放下注';
    const MESSAGE_GAME_END = '新的一期將在&nbsp;{sec}&nbsp;秒後開始';

    public function index()
    {
        $view = view('index');
        $view->msg = session()->pull('msg', 'No message!');

        return $view;
    }

    public function setCash($account, $cash)
    {
        if (Auth::user()->account == 'max') {
            $user_model = new User;
            $user_model->setCashByAccount($account, $cash);

            return Redirect::intended('/');
        } else {
            return "You have no permission to do that!!";
        }
    }

    public function getGameData()
    {
        try {
            $today = Date('Ymd');
            $game_model = new Game;
            $round_model = new Round;
            $game = $game_model->getCurrentGame($today);
            $rounds = $round_model->getRoundByGameNo($game->no);

            $round_size = 0;
            if (sizeof($rounds) - 1 > 0) {
                $round_size = sizeof($rounds) - 1;
            }

            $game->aa = sizeof($rounds);

            $round_last = $rounds[$round_size];
            $game->round = $rounds;

            $game->timer = $round_last->end_at - time();
            $game->msg = str_replace('{index}', ($round_size + 1), self::MESSAGE_ROUND_RUNNING);

            if ($game->state == 0) {
                if ($round_last->state == 2) {
                    $next_round_start_at = $round_last->end_at + gameSettings('ROUND_INTERVAL');
                    $game->timer = $next_round_start_at - time();
                    $game->msg = str_replace('{index}', ($round_size + 2), self::MESSAGE_ROUND_END);
                }
            }

            if ($game->state == 2) {
                $next_game_start_at = $round_last->end_at + gameSettings('GAME_INTERVAL');
                $game->timer = $next_game_start_at - time();
                $game->msg = self::MESSAGE_GAME_END;
            }

            $game->odds = calcOdds($round_last->current_min, $round_last->current_max);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $game->toJson();
    }

    public function getFinalCode($games_no)
    {
        $data_array = ['final_code' => '?', 'big_winner' => '?'];
        $game_model = new Game;
        $game_data = $game_model->getGameByNoState($games_no, gameSettings('STATE_CLOSED'));

        if ($game_data) {
            $data_array['final_code'] = $game_data->final_code;

            $bet_detail_model = new BetDetail;
            $big_winner = $bet_detail_model->getBigWinnerByGamesNo($games_no);
            if (!is_null($big_winner)) {
                $data_array['big_winner'] = $bet_detail_model->getBigWinnerByGamesNo($games_no);
            }

        }

        return json_encode($data_array);
    }

    public function playerBet(Request $request)
    {
        $is_pass = true;
        $games_no = $request->input('games_no');
        $round_no = $request->input('round_no');
        $bet_part1 = $request->input('bet_part1');
        $num_types = $request->input('numType');
        $bet_part2 = $request->input('bet_part2');
        $numbers = $request->input('numbers');

        $count_of_numbers = sizeof($numbers);
        $count_of_num_types = sizeof($num_types);

        $user = null;

        if ($is_pass) {
            if (is_null($num_types) && is_null($numbers)) {
                $is_pass = false;
                session()->put('msg', "至少須選擇一種玩法進行下注。");
            }
        }

        if ($is_pass) {
            if ((!is_null($num_types) && $bet_part1 <= 0) || (!is_null($numbers) && $bet_part2 <= 0)) {
                $is_pass = false;
                session()->put('msg', "請輸入下注金額。");
            }
        }

        if ($is_pass) {
            $user_model = new User;
            $user = $user_model->getUserByAccount(Auth::user()->account);
            if (($bet_part1 * $count_of_num_types) + ($bet_part2 * $count_of_numbers) > $user->cash) {
                $is_pass = false;
                session()->put('msg', "您所擁有可下注金額不足。");
            }
        }

        if ($is_pass) {
            $game_model = new Game;
            $round_model = new Round;
            $game = $game_model->getGameByNoState($games_no, gameSettings('STATE_RUNNING'));
            $round = $round_model->getRoundByGameNoRound($games_no, $round_no);
            if ($game && $round) {
                $code_range_min = $round->current_min;
                $code_range_max = $round->current_max;
                $odds = calcOdds($code_range_min, $code_range_max);

                $odds_odd = $odds['odd'];
                $odds_even = $odds['even'];
                $odds_numbers = $odds['numbers'];

                if ((!is_null($numbers) && $bet_part2 > 0)) {
                    foreach ($numbers as $number) {
                        $bet_detail_model = new BetDetail;
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
                    }

                    $user->cash = $user->cash - ($count_of_numbers * $bet_part2);
                    $user->save();
                    session()->put('msg', "下注成功。");
                }

                if ((!is_null($num_types) && $bet_part1 > 0)) {
                    foreach ($num_types as $num_type) {
                        $bet_detail_model = new BetDetail;
                        $bet_detail_model->user_id = $user->id;
                        $bet_detail_model->games_no = $games_no;
                        $bet_detail_model->round = $round_no;
                        $bet_detail_model->bet_at = time();
                        $bet_detail_model->win_cash = 0;
                        $bet_detail_model->part = 1;
                        $bet_detail_model->guess = $num_type;
                        $bet_detail_model->bet = $bet_part1;
                        $bet_detail_model->odds = $odds_odd;
                        if ($num_type % 2 == 0) {
                            $bet_detail_model->odds = $odds_even;
                        }
                        $bet_detail_model->save();
                    }

                    $user->cash = $user->cash - ($count_of_num_types * $bet_part1);
                    $user->save();
                    session()->put('msg', "下注成功。");
                }

            } else {
                session()->put('msg', "目前尚無進行中的遊戲，無法進行下注。");
            }
        }

        return Redirect::intended('/');
    }

    public function getBetHistory($game_no)
    {
        $bet_detail_model = new BetDetail;
        $game_model = new Game;
        $bet_details = $bet_detail_model->getByUserIdGamesNo(Auth::User()->id, $game_no);

        $bet_array = [];
        foreach ($bet_details as $bet) {
            $game = $game_model->getGameByNoState($bet->games_no, gameSettings('STATE_CLOSED'));

            if ($game) {
                $bet->final_code = $game->final_code;
            } else {
                $bet->final_code = '??';
            }

            $bet_array[] = $bet;
        }

        $user_model = new User;
        $user = $user_model->getUserByAccount(Auth::user()->account);
        $bet_array['cash'] = $user->cash;

        return json_encode($bet_array);
    }

    public function billingRound()
    {
        $bet_detail_model = new BetDetail;
        $user_model = new User;
        $round_model = new Round;
        $bet_details = $bet_detail_model->getNotFinishedByPart(1);
        foreach ($bet_details as $bet) {
            $round = $round_model->getRoundByGameNoRound($bet->games_no, $bet->round);
            $round_code_type = $round->round_code % 2;
            $win_cash = $bet->bet * -1;
            if ($bet->guess % 2 == $round_code_type) {
                $win_cash = $bet->bet * $bet->odds;
            }

            $bet->win_cash = $win_cash;
            $bet->save();

            if ($win_cash > 0) {
                $user_model->setCashById($bet->user_id, $bet->win_cash);
            }
        }

        return $bet_details;
    }

    public function billingGame()
    {
        $bet_detail_model = new BetDetail;
        $user_model = new User;
        $game_model = new Game;
        $bet_details = $bet_detail_model->getNotFinishedByPart(2);
        foreach ($bet_details as $bet) {
            $game = $game_model->getGameByNoState($bet->games_no, gameSettings('STATE_CLOSED'));
            $win_cash = $bet->bet * -1;
            if ($bet->guess == $game->final_code) {
                $win_cash = $bet->bet * $bet->odds;
            }

            $bet->win_cash = $win_cash;
            $bet->save();

            if ($win_cash > 0) {
                $user_model->setCashById($bet->user_id, $bet->win_cash);
            }
        }
    }

    public function settings(){
        if(Auth::user()->account != 'max'){
            session()->put('msg', "您目前尚未擁有權限進行此項動作。");
            return Redirect::intended('/');
        }else{
            $settings_model = new Settings;
            $settings = $settings_model->all();
            $view = view('settings');
            $view->settings = $settings;
            return $view;
        }
    }
}
