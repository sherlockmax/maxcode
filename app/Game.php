<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $hidden = ['id', 'state', 'start_at', 'memo', 'final_code'];

    public $timestamps = false;

    /**
     * 取得今日最新的一期遊戲
     *
     * @param $today　今日（期數格式）ex.20160202
     * @return mixed
     */
    public function getCurrentGame($today)
    {
        return Game
            ::where('no', 'like', "$today%")
            ->orderBy('no', 'desc')->first();
    }

    /**
     * 根據期數及狀態取得遊戲
     *
     * @param $no　期數
     * @param $state　狀態
     * @return mixed
     */
    public function getGameByNoState($no, $state)
    {
        return Game
            ::where('no', $no)
            ->where('state', $state)
            ->first();
    }

    /**
     * 取得最新且已關閉的遊戲
     *
     * @return mixed
     */
    public function getLastClosedGameNo()
    {
        return Game
            ::where('state', gameSettings('STATE_CLOSED'))
            ->max('no');
    }

    /**
     * 根據期數取得遊戲
     *
     * @param $no　期數
     * @return mixed
     */
    public function getGameByNo($no)
    {
        return Game
            ::where('no', $no)
            ->first();
    }

    /**
     * 根據遊戲ID取得遊戲
     *
     * @param $id　遊戲ID
     * @return mixed
     */
    public function getClosedGameById($id)
    {
        return Game
            ::where('id', $id)
            ->where('state', gameSettings('STATE_CLOSED'))
            ->first();
    }

    public static function statisticsFinalCode($start_games_no, $end_games_no)
    {
        $state_closed = gameSettings('STATE_CLOSED');

        $query = Game::where('state', $state_closed);

        if($start_games_no != 'all'){
            $query->where('no', '>=', $start_games_no);
        }

        if($end_games_no != 'all'){
            $query->where('no', '<=', $end_games_no);
        }

        return $query
            ->select(\DB::raw("`final_code`, COUNT(*) AS `times`"))
            ->groupBy('final_code')
            ->get();
    }

    public static function countOfClosedGames($start_games_no, $end_games_no)
    {
        $state_closed = gameSettings('STATE_CLOSED');

        $query = Game::where('state', $state_closed);

        if($start_games_no != 'all'){
            $query->where('no', '>=', $start_games_no);
        }

        if($end_games_no != 'all'){
            $query->where('no', '<=', $end_games_no);
        }

        return $query->count();
    }

    public static function getMaxClosedByColumnName($column_name, $start_games_no, $end_games_no)
    {
        $state_closed = gameSettings('STATE_CLOSED');

        $query = Game::where('state', $state_closed);

        if($start_games_no != 'all'){
            $query->where('no', '>=', $start_games_no);
        }

        if($end_games_no != 'all'){
            $query->where('no', '<=', $end_games_no);
        }

        return $query->max($column_name);
    }

    public static function getMinClosedByColumnName($column_name, $start_games_no, $end_games_no)
    {
        $state_closed = gameSettings('STATE_CLOSED');

        $query = Game::where('state', $state_closed);

        if($start_games_no != 'all'){
            $query->where('no', '>=', $start_games_no);
        }

        if($end_games_no != 'all'){
            $query->where('no', '<=', $end_games_no);
        }

        return $query->min($column_name);
    }

    public static function getLastNoByDate($date)
    {
        return Game
            ::where('no', 'like', "$date%")
            ->max('no');
    }


    public static function getDateList(){
        $state_closed = gameSettings('STATE_CLOSED');

        $result = Game
            ::select(\DB::raw("SUBSTRING(`no`, 1, 8) AS `date`"))
            ->where('state', $state_closed)
            ->groupBy(\DB::raw("SUBSTRING(`no`, 1, 8)"))
            ->get();

        return $result;
    }

    public static function getGamesNoList($date){
        $state_closed = gameSettings('STATE_CLOSED');

        $result = Game
            ::select(\DB::raw("SUBSTRING(`no`, 9, 4) AS `no`"))
            ->where('state', $state_closed)
            ->where('no', 'like', "$date%")
            ->groupBy(\DB::raw("SUBSTRING(`no`, 9, 4)"))
            ->get();

        return $result;
    }
}
