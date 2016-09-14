<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \DB;

class BetDetail extends Model
{
    protected $table = 'bet_details';

    public $timestamps = false;

    /**
     * 根據使用者ID及遊戲期數取得下注資料
     *
     * @param $user_id　使用者ID
     * @param $games_no　遊戲期數
     * @return mixed
     */
    public function getByUserIdGamesNo($user_id, $games_no)
    {
        return DB::table('bet_details')
            ->join('rounds', function ($join) {
                $join->on('rounds.games_no', '=', 'bet_details.games_no')
                    ->on('rounds.round', '=', 'bet_details.round');
            })
            ->where('bet_details.user_id', $user_id)
            ->where('bet_details.games_no', $games_no)
            ->orderBy('bet_details.games_no', 'DESC')
            ->orderBy('bet_details.round', 'DESC')
            ->orderBy('bet_details.part', 'ASC')
            ->select('bet_details.*', 'rounds.round_code')
            ->get();
    }

    /**
     * 根據期數計算並取得該期贏得最多獎金的玩家
     *
     * @param $games_no
     * @return mixed
     */
    public function getBigWinnerByGamesNo($games_no)
    {
        return DB::table('bet_details')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'bet_details.user_id');
            })
            ->where('bet_details.games_no', $games_no)
            ->where('bet_details.win_cash', '>', 0)
            ->groupBy('users.id')
            ->select([
                'bet_details.games_no',
                'users.name',
                DB::raw('SUM(bet_details.win_cash) AS win_cash')
            ])
            ->orderBy('win_cash', 'DESC')
            ->first();
    }

    /**
     * 根據期數、回合及回合密碼結算單雙下注單
     *
     * @param $games_no　期數
     * @param $round　回合
     * @param $code　回合密碼
     */
    public function billingRound($games_no, $round, $code)
    {
        self::where('games_no', $games_no)
            ->where('round', $round)
            ->where('part', 1)
            ->where('win_cash', 0)
            ->update(
                array(
                    'win_cash' =>
                        DB::raw(
                            "(CASE WHEN `guess` = $code THEN `bet` * `odds` ELSE `bet` * -1 END)"
                        ),
                    'is_grant' =>
                        DB::raw(
                            "(CASE WHEN `guess` = $code THEN 0 ELSE 2 END)"
                        )
                )
            );
    }

    /**
     * 根據期數及終極密碼結算選號下注單
     *
     * @param $games_no　期數
     * @param $code　終極密碼
     */
    public function billingGame($games_no, $code)
    {
        self::where('games_no', $games_no)
            ->where('part', 2)
            ->where('win_cash', 0)
            ->update(
                array(
                    'win_cash' =>
                        DB::raw(
                            "(CASE WHEN `guess` = $code THEN `bet` * `odds` ELSE `bet` * -1 END)"
                        ),
                    'is_grant' =>
                        DB::raw(
                            "(CASE WHEN `guess` = $code THEN 0 ELSE 2 END)"
                        )
                )
            );
    }
}
