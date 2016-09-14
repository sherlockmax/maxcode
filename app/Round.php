<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $hidden = ['games_no', 'state'];

    public $timestamps = false;

    /**
     * 根據期數取得所有回合
     *
     * @param $game_no　遊戲期數
     * @return mixed
     */
    public function getRoundByGameNo($game_no)
    {
        return Round
            ::where('games_no', $game_no)
            ->orderBy('round', 'asc')->get();
    }

    /**
     * 根據期數及回合取得回合
     *
     * @param $game_no　遊戲期數
     * @param $round　回合
     * @return mixed
     */
    public function getRoundByGameNoRound($game_no, $round)
    {
        return Round
            ::where('games_no', $game_no)
            ->orderBy('round', $round)
            ->first();
    }
}
