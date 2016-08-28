<?php

namespace App\Console\Commands;

use App\Game;
use App\Round;

class GameServer
{
    const TIME_OF_ROUND = 30;
    const ROUND_PER_GAME = 3;

    const GAME_INTERVAL = 10;
    const ROUND_INTERVAL = 10;

    const CODE_RANGE_MIN = 1;
    const CODE_RANGE_MAX = 40;

    const IS_DEBUG_MODE = true;
    const STATE_RUNNING = 0;
    const STATE_CLOSING = 1;
    const STATE_CLOSED = 2;

    private $game_data;
    private $game_date;
    private $game_index = 1;

    private function getRandCode(){
        return rand(self::CODE_RANGE_MIN, self::CODE_RANGE_MAX);
    }

    private function gameStart(){
        $this->game_data = null;
        if($this->game_date != Date('Ymd')) {
            $this->game_date = Date('Ymd');
            $this->gameIndex = 1;
        }

        $game_full_index = str_pad($this->game_index, 4, "0", STR_PAD_LEFT);
        $game_full_no = $this->game_date . $game_full_index;

        $this->game_data = [
            'no' => $game_full_no,
            'final_code' => $this->getRandCode(),
            'state' => self::STATE_RUNNING,
            'round' => [],
        ];

        $game = new Game;
        $game->no = $this->game_data['no'];
        $game->final_code = $this->game_data['final_code'];
        $game->state = self::STATE_RUNNING;
        $game->save();

        $this->game_index++;
    }

    private function gameClosed(){
            $game_date = $this->game_date;
            $game_index = str_pad($this->game_index - 1, 4, "0", STR_PAD_LEFT);
            $game = Game::where('no', $game_date . $game_index);
            $game->update(['state' => self::STATE_CLOSED]);
    }

    private function roundStart(){

        $start_timestamp = time();
        $end_timestamp = time() + self::TIME_OF_ROUND;

        $this->game_data['round'][] = [
            'round_no' => sizeof($this->game_data['round'])+1,
            'start_at' => Date("Y-m-d H:i:s", $start_timestamp),
            'end_at' => Date("Y-m-d H:i:s", $end_timestamp),
            'code' => 0,
            'state' => self::STATE_RUNNING,
        ];

        $round = new Round;
        $round->games_no = $this->game_data['no'];
        $round->round = $this->game_data['round'][sizeof($this->game_data['round'])-1]['round_no'];
        $round->state = self::STATE_RUNNING;
        $round->round_code = $this->game_data['round'][sizeof($this->game_data['round'])-1]['code'];
        $round->start_at = $this->game_data['round'][sizeof($this->game_data['round'])-1]['start_at'];
        $round->end_at = $this->game_data['round'][sizeof($this->game_data['round'])-1]['end_at'];
        $round->save();
    }

    private function roundClosed()
    {
        $round_count = sizeof($this->game_data['round']);
        $this->game_data['round'][$round_count-1]['code'] = $this->getRandCode();
        $this->game_data['round'][$round_count-1]['state'] = self::STATE_CLOSED;

        $round = Round
            ::where('games_no', $this->game_data['no'])
            ->where('round', $round_count);
        $round->update(['state' => self::STATE_CLOSED, 'round_code' => $this->game_data['round'][$round_count-1]['code']]);
    }

    private function init()
    {
        if(Game::all()->count() > 0) {
            $today = Date("Ymd");
            $game = Game::where('no', 'like', $today . '%')->orderBy('no', 'desc')->first();
            $maxIndex = ((int)str_replace($today, '', $game->no)) + 1;
            $this->game_index = $maxIndex;
        }
    }

    private function startServer(){

        $this->gameStart();

        $this->echoGameData("Game Start");

        while (sizeof($this->game_data['round']) < self::ROUND_PER_GAME) {

            $this->roundStart();

            $this->echoGameData("Round Start");

            sleep(self::TIME_OF_ROUND);

            $this->roundClosed();

            $this->echoGameData("Round Closed");

            if(sizeof($this->game_data['round']) < self::ROUND_PER_GAME - 1){
                sleep(self::ROUND_INTERVAL);
            }
        }

        $this->gameClosed();

        $this->echoGameData("Game Closed");

        sleep(self::GAME_INTERVAL);
        $this->startServer();
    }

    public function run()
    {
        $this->init();

        $this->startServer();
    }


    private function echoGameData($title = ""){
        if(self::IS_DEBUG_MODE){
            print("$title " . Date("Y-m-d H:i:s") . " -------------\n");
            print_r($this->game_data);
            print("---$title " . Date("Y-m-d H:i:s") . " -------------\n");
            flush();
        }
    }
}
