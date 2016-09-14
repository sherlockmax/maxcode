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
        $view->bet_detail_count = 0;
        $view->bet_total = 0;
        $view->bet_win_total = 0;

        if (is_null($game)) {
            $view->msg = "查無該期遊戲資料。";
        } else {
            if ($game->state == config('gameset.STATE_CLOSED')) {
                $view->game = $game;

                $round_model = new Round;
                $rounds = $round_model->getRoundByGameNo($game->no);
                $view->rounds = $rounds;


                $bet_detail_model = new BetDetail;
                $bet_details = $bet_detail_model->getByUserIdGamesNo(Auth::user()->id, $game->no);
                if (!is_null($bet_details)) {
                    $view->bet_details = $bet_details;
                    $view->bet_detail_count = sizeof($bet_details);

                    foreach ($bet_details as $bet) {
                        $view->bet_total += $bet->bet;
                        if ($bet->win_cash > 0) {
                            $view->bet_win_total += $bet->win_cash;
                        }
                    }
                }
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
