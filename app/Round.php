<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $hidden = ['games_no', 'state'];

    public $timestamps = false;

    public function getRoundByGameNo($game_no){
        return Round
            ::where('games_no', $game_no)
            ->orderBy('round', 'asc')->get();
    }

    public function getRoundByGameNoRound($game_no, $round){
        return Round
            ::where('games_no', $game_no)
            ->orderBy('round', $round)
            ->first();
    }
}
