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
}
