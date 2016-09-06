<?php

namespace App\Http\Controllers;

use Auth;
use App\Game;
use App\Round;
use App\BetDetail;

class RecordController extends Controller
{
    public function index()
    {
        $game_model = new Game;
        $games_no = $game_model->getLastClosedGameNo();

        return $this->record($games_no);
    }

    public function record($games_no)
    {
        $game_model = new Game;
        $game = $game_model->getGameByNo($games_no);

        $view = view('record');

        $view->games_no = $games_no;
        $view->next = 0;
        $view->last = 0;
        $view->game = null;
        $view->rounds = null;
        $view->bet_details = null;

        if (is_null($game)) {
            $view->msg = "查無該期遊戲資料。";
        } else {
            if ($game->state == config('gameset.STATE_CLOSED')) {
                $view->game = $game;

                $round_model = new Round;
                $rounds = $round_model->getRoundByGameNo($game->no);
                $view->rounds = $rounds;


                $bet_detail_model = new BetDetail;
                $view->bet_details = $bet_detail_model->getByUserGameNo(Auth::user()->id, $game->no);
            } else {
                $view->msg = "該期遊戲尚未結束，無法查詢。";
            }

            $game_tmp = $game_model->getClosedGameById($game->id - 1);
            if (!is_null($game_tmp)) {
                $view->last = $game_tmp->no;
            }
            $game_tmp = $game_model->getClosedGameById($game->id + 1);
            if (!is_null($game_tmp)) {
                $view->next = $game_tmp->no;
            }
        }

        return $view;
    }
}
