<?php

namespace App\Console\Commands;

use App\Game;
use App\Round;

class GameServer
{
    const GAME_INTERVAL = 10;
    const ROUND_PER_GAME = 3;

    const ROUND_PER_SEC = 30;
    const ROUND_INTERVAL = 10;

    const CODE_RANGE_MIN = 1;
    const CODE_RANGE_MAX = 40;

    const IS_DEBUG_MODE = true;

    private $game_data;
    private $game_date;
    private $game_index = 1;

    private function getRandCode(){
        return rand(self::CODE_RANGE_MIN, self::CODE_RANGE_MAX);
    }

    private function createNewGame(){
        $this->game_data = null;
        if($this->game_date != Date('Ymd')) {
            $this->game_date = Date('Ymd');
            $gameIndex = 1;
        }

        $game_full_index = str_pad($this->game_index, 4, "0", STR_PAD_LEFT);
        $game_full_no = $this->game_date . $game_full_index;

        $this->game_data = [
            'no' => $game_full_no,
            'final_code' => $this->getRandCode(),
            'state' => 1,
            'round' => [],
        ];

        $game = new Game;
        $game->no = $this->game_data['no'];
        $game->final_code = $this->game_data['final_code'];
        $game->state = $this->game_data['state'];
        $game->save();

        $this->game_index++;
    }

    private function createNewRound(){

        $start_timestamp = time();
        $end_timestamp = time() + self::ROUND_PER_SEC;

        $this->game_data['round'][] = [
            'round_no' => sizeof($this->game_data['round'])+1,
            'start_at' => Date("Y-m-d H:i:s", $start_timestamp),
            'end_at' => Date("Y-m-d H:i:s", $end_timestamp),
            'code' => 0
        ];

        $round = new Round;
        $round->games_no = $this->game_data['no'];
        $round->round = $this->game_data['round'][sizeof($this->game_data['round'])-1]['round_no'];
        $round->round_code = $this->game_data['round'][sizeof($this->game_data['round'])-1]['code'];
        $round->start_at = $this->game_data['round'][sizeof($this->game_data['round'])-1]['start_at'];
        $round->end_at = $this->game_data['round'][sizeof($this->game_data['round'])-1]['end_at'];
        $round->save();

        $this->echoGameData("Create new Round");
        sleep(self::ROUND_PER_SEC);

        $round_count = sizeof($this->game_data['round']);
        $this->game_data['round'][$round_count-1]['code'] = $this->getRandCode();
        $this->echoGameData("Get code");

        Round::where('games_no', $this->game_data['no'])
            ->where('round', $this->game_data['round'][$round_count-1]['round_no'])
            ->update(['round_code' => $this->game_data['round'][$round_count-1]['code']]);

        if($round_count < self::ROUND_PER_GAME){
            if($round_count > 0){
                sleep(self::ROUND_INTERVAL);
            }
            $this->createNewRound();
        }
    }

    private function echoGameData($title = ""){
        if(self::IS_DEBUG_MODE){
            print("Debug mode -$title- " . Date("Y-m-d H:i:s") . " -------------\n");
            print_r($this->game_data);
            flush();
        }
    }

    public function run()
    {
        $this->createNewGame();

        $this->echoGameData("New Game");

        $this->createNewRound();

        $this->echoGameData("Game END");

        sleep(self::GAME_INTERVAL);
        $this->run();
    }
}
