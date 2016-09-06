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
}
