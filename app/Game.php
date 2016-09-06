<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $hidden = ['id', 'state', 'start_at', 'memo', 'final_code'];

    public $timestamps = false;

    public function getCurrentGame($today)
    {
        return Game
            ::where('no', 'like', "$today%")
            ->orderBy('no', 'desc')->first();
    }

    public function getGameByNoState($no, $state)
    {
        return Game
            ::where('no', $no)
            ->where('state', $state)
            ->first();
    }

    public function getLastClosedGameNo()
    {
        return Game
            ::where('state', config('gameset.STATE_CLOSED'))
            ->max('no');
    }

    public function getGameByNo($no)
    {
        return Game
            ::where('no', $no)
            ->first();
    }

    public function getClosedGameById($id)
    {
        return Game
            ::where('id', $id)
            ->where('state', config('gameset.STATE_CLOSED'))
            ->first();
    }
}
