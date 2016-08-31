<?php

namespace App\Console\Commands;

use App\Game;
use App\Round;
use \DB;

class GameServer
{
    const IS_DEBUG_MODE = true;

    private $game_data;
    private $game_date;
    private $game_index = 1;
    private $memo;

    private function getRandCode($min = 0, $max = 0)
    {
        if($min == 0){
            $min = config('gameset.CODE_RANGE_MIN');
        }
        if($max == 0){
            $max = config('gameset.CODE_RANGE_MAX');
        }
        return rand($min, $max);
    }

    private function gameStart()
    {
        $final_code = $this->getRandCode();

        $this->game_data = null;
        if ($this->game_date != Date('Ymd')) {
            $this->game_date = Date('Ymd');
            $this->game_index = 1;
        }

        $game_full_index = str_pad($this->game_index, 4, "0", STR_PAD_LEFT);
        $game_full_no = $this->game_date . $game_full_index;

        $this->game_data = [
            'no' => $game_full_no,
            'final_code' => $final_code,
            'state' => config('gameset.STATE_RUNNING'),
            'current_min' => config('gameset.CODE_RANGE_MIN'),
            'current_max' => config('gameset.CODE_RANGE_MAX'),
            'round' => [],
        ];

        $game = new Game;
        $game->no = $this->game_data['no'];
        $game->final_code = $final_code;
        $game->state = config('gameset.STATE_RUNNING');
        $game->current_min = config('gameset.CODE_RANGE_MIN');
        $game->current_max = config('gameset.CODE_RANGE_MAX');
        $game->save();

        $this->game_index++;
    }

    private function gameClosed()
    {
        $this->game_data['state'] = config('gameset.STATE_CLOSED');

        $game_date = $this->game_date;
        $game_index = str_pad($this->game_index - 1, 4, "0", STR_PAD_LEFT);
        $game = Game::where('no', $game_date . $game_index);
        if(is_null($this->memo)){
            $this->memo = 'no winner';
        }
        $game->update(['state' => config('gameset.STATE_CLOSED'), 'memo' => $this->memo]);
        $this->memo = null;
    }

    private function roundStart()
    {
        $start_timestamp = time();
        $end_timestamp = time() + config('gameset.TIME_OF_ROUND');

        $this->game_data['round'][] = [
            'round_no' => sizeof($this->game_data['round']) + 1,
            'start_at' => Date("Y-m-d H:i:s", $start_timestamp),
            'end_at' => Date("Y-m-d H:i:s", $end_timestamp),
            'code' => 0,
            'state' => config('gameset.STATE_RUNNING'),
        ];

        $round = new Round;
        $round->games_no = $this->game_data['no'];
        $round->round = $this->game_data['round'][sizeof($this->game_data['round']) - 1]['round_no'];
        $round->state = config('gameset.STATE_RUNNING');
        $round->round_code = $this->game_data['round'][sizeof($this->game_data['round']) - 1]['code'];
        $round->start_at = $this->game_data['round'][sizeof($this->game_data['round']) - 1]['start_at'];
        $round->end_at = $this->game_data['round'][sizeof($this->game_data['round']) - 1]['end_at'];
        $round->save();
    }

    private function roundClosed()
    {
        $rand_min = $this->game_data['current_min'] + 1;
        $rand_max = $this->game_data['current_max'] - 1;

        $round_code = $this->getRandCode($rand_min, $rand_max);
        $round_count = sizeof($this->game_data['round']);
        $this->game_data['round'][$round_count - 1]['code'] = $round_code;
        $this->game_data['round'][$round_count - 1]['state'] = config('gameset.STATE_CLOSED');

        $round = Round
            ::where('games_no', $this->game_data['no'])
            ->where('round', $round_count);
        $round->update([
            'state' => config('gameset.STATE_CLOSED'),
            'round_code' => $round_code
        ]);

        return $round_code;
    }

    private function init()
    {
        DB::table('games')
            ->where('state', '!=', config('gameset.STATE_CLOSED'))
            ->update(['state' => config('gameset.STATE_CLOSED')]);
        DB::table('rounds')
            ->where('state', '!=', config('gameset.STATE_CLOSED'))
            ->update(['state' => config('gameset.STATE_CLOSED')]);

        $this->game_date = Date("Ymd");
        $game = Game::where('no', 'like', $this->game_date . '%')->orderBy('no', 'desc');
        if ($game->count() > 0) {
            $maxIndex = ((int)str_replace($this->game_date, '', $game->first()->no)) + 1;
            $this->game_index = $maxIndex;
        }
    }

    private function startServer()
    {
        $this->gameStart();
        $this->echoGameData("Game Start");

        while (sizeof($this->game_data['round']) < config('gameset.ROUND_PER_GAME')) {
            $this->roundStart();
            $this->echoGameData("Round Start");

            sleep(config('gameset.TIME_OF_ROUND'));

            $round_code = $this->roundClosed();
            $this->echoGameData("Round Closed");

            if($round_code == $this->game_data['final_code']){
                $this->memo = 'round code eq final code';
                break;
            }

            if($round_code < $this->game_data['final_code']){
                $this->game_data['current_min'] = $round_code;

                Game::where('no', $this->game_data['no'])
                    ->update(['current_min' => $round_code]);
            }

            if($round_code > $this->game_data['final_code']){
                $this->game_data['current_max'] = $round_code;

                Game::where('no', $this->game_data['no'])
                    ->update(['current_max' => $round_code]);
            }

            if($this->game_data['current_max'] - $this->game_data['current_min'] <= 1){
                $this->memo = 'current max and min code\'s range are left 1 numbers';
                break;
            }

            if (sizeof($this->game_data['round']) < config('gameset.ROUND_PER_GAME')) {
                sleep(config('gameset.ROUND_INTERVAL'));
            }
        }

        $this->gameClosed();
        $this->echoGameData("Game Closed");

        sleep(config('gameset.GAME_INTERVAL'));
        $this->startServer();
    }

    public function run()
    {
        $this->init();

        $this->startServer();
    }

    private function echoGameData($title = "")
    {
        if (self::IS_DEBUG_MODE) {
            print("$title " . Date("Y-m-d H:i:s") . " -------------\n");
            print_r($this->game_data);
            print("---$title " . Date("Y-m-d H:i:s") . " -------------\n");
            flush();
        }
    }
}
