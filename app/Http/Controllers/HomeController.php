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

    /**
     * 利用get方式直接將使用者的cash加上輸入的金額
     *　localhost/setCash/{account}/{cash}
     *
     * @param $account
     * @param $cash
     * @return string
     */
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

    /**
     * 取得目前正在執行的遊戲資訊
     *
     * @method POST
     * @return string
     */
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

            try{
                $game->is_settings_changed = Redis::get('is_setting_changed');
                Redis::set('is_setting_changed', 'false');
            }catch (\Exception $e){
                $game->is_settings_changed = false;
            }
            $game->odds = calcOdds($round_last->current_min, $round_last->current_max);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $game->toJson();
    }

    /**
     * 取得指定期數的終極密碼
     *
     * @param $games_no　期數
     * @return string
     */
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

    /**
     * 下注
     *
     * @method POST
     * @param Request $request
     * @return mixed
     */
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
                if(time() < $round->end_at && time() >= $round->start_at) {
                    $code_range_min = $round->current_min;
                    $code_range_max = $round->current_max;
                    $odds = calcOdds($code_range_min, $code_range_max);

                    $odds_odd = $odds['odd'];
                    $odds_even = $odds['even'];
                    $odds_numbers = $odds['numbers'];

                    try {
                        DB::beginTransaction();
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
                        }
                        session()->put('msg', "下注成功。");
                        DB::commit();
                    } catch (\Exception $err) {
                        DB::rollBack();
                        session()->put('msg', "下注失敗，請確認後再試一次。");
                    }
                }else{
                    session()->put('msg', "下注時間已結束。");
                }
            } else {
                session()->put('msg', "目前尚無進行中的遊戲，無法進行下注。");
            }
        }

        return Redirect::intended('/');
    }

    /**
     * 取得登入者的指定期數的下注資訊
     *
     * @param $game_no　期數
     * @return string
     */
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

    /**
     * 取得設定檔資訊並回傳至設定頁面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings()
    {
        if (Auth::user()->account != 'max') {
            session()->put('msg', "您目前尚未擁有權限進行此項動作。");
            return Redirect::intended('/');
        } else {
            $settings_model = new Settings;
            $settings = $settings_model->all();
            $view = view('settings');
            $view->settings = $settings;
            $view->msg = session()->pull('msg', 'No message!');
            return $view;
        }
    }

    /**
     * 設定設定檔
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setSettings(Request $request)
    {
        $input = $request->input();
        unset($input['_token']);
        //$settings = Settings::create($input);
        $settings = new Settings;
        $settings->updateAll($input);

        session()->put('msg', "更新遊戲設定完成。");
        return $this->settings();
    }

    /**
     * 結算單雙/選號玩法的注單
     *
     * @param $games_no 遊戲期數
     * @param $round 　回合
     * @param $part 　玩法1：單雙/2：選號
     * @param $code 　回合密碼/終極密碼
     */
    public function billing($games_no, $round, $code)
    {
        $bet_details_model = new BetDetail;
        if ($round == 'all') {
            $bet_details_model->billingGame($games_no, $code);
        } else {
            $bet_details_model->billingRound($games_no, $round, $code);
        }

    }
}
