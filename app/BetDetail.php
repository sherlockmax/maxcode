<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \DB;

class BetDetail extends Model
{
    //
    protected $table = 'bet_details';

    public $timestamps = false;

    public function getByUserId($user_id)
    {
        return DB::table('bet_details')
            ->join('rounds', function ($join) {
                $join->on('rounds.games_no', '=', 'bet_details.games_no')
                    ->on('rounds.round', '=', 'bet_details.round');
            })
            ->where('bet_details.user_id', $user_id)
            ->orderBy('bet_details.games_no', 'DESC')
            ->orderBy('bet_details.round', 'DESC')
            ->orderBy('bet_details.part', 'ASC')
            ->take(11)
            ->select('bet_details.*', 'rounds.round_code')
            ->get();
    }

    public function getNotFinishedByPart($part)
    {
        return BetDetail
            ::where('win_cash', 0)
            ->where('part', $part)
            ->get();
    }

    public function getByUniqueField($games_no, $user_id, $round, $part)
    {
        return BetDetail
            ::where('games_no', $games_no)
            ->where('user_id', $user_id)
            ->where('round', $round)
            ->where('part', $part)
            ->first();
    }

    public function getBigWinnerByGamesNo($games_no)
    {
        return DB::table('bet_details')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'bet_details.user_id');
            })
            ->where('bet_details.games_no', $games_no)
            ->where('bet_details.part', 2)
            ->where('bet_details.win_cash', '>', 0)
            ->select([
                'bet_details.win_cash',
                'bet_details.games_no',
                'users.name',
                DB::raw('MAX(bet_details.win_cash) AS win_cash')
            ])
            ->first();
    }
}
