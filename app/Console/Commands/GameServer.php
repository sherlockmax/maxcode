<?php

namespace App\Console\Commands;

use App\Game;
use App\Round;
use App\Settings;
use \DB;
use \App;

class GameServer
{
    const IS_DEBUG_MODE = true;

    private $game_date;
    private $game_index = 1;
    private $game_no;
    private $current_min;
    private $current_max;
    private $memo;

    /**
     * 於指定範圍中，亂數取得一數
     *
     * @param int $min　最小值
     * @param int $max　最大值
     * @return int
     */
    private function getRandCode($min = 0, $max = 0)
    {
        if ($min == 0) {
            $min = gameSettings('CODE_RANGE_MIN');
        }
        if ($max == 0) {
            $max = gameSettings('CODE_RANGE_MAX');
        }
        return mt_rand($min, $max);
    }

    /**
     * 建立新的一期遊戲，將資料寫到資料庫後，回傳終極密碼
     *
     * @return int
     */
    private function gameStart()
    {
        if ($this->game_date != Date('Ymd')) {
            $this->game_date = Date('Ymd');
            $this->game_index = 1;
        }

        $game_full_index = str_pad($this->game_index, 4, "0", STR_PAD_LEFT);
        $this->game_no = $this->game_date . $game_full_index;

        $game = new Game;
        $game->no = $this->game_no;
        $game->final_code = $this->getRandCode();
        $game->state = gameSettings('STATE_RUNNING');
        $game->start_at = time();
        $game->memo = '';
        $game->save();

        $this->game_index++;

        return $game->final_code;
    }

    /**
     *　將該期遊戲結束，並寫入結束原因
     */
    private function gameClosed()
    {
        $game = Game::where('no', $this->game_no);
        if (is_null($this->memo)) {
            $this->memo = 'no winner';
        }
        $game->update(['state' => gameSettings('STATE_CLOSED'), 'memo' => $this->memo]);
        $this->memo = null;
    }

    /**
     * 建立新的回合，將資料寫到資料庫
     */
    private function roundStart()
    {
        $start_timestamp = time();
        $end_timestamp = $start_timestamp + gameSettings('TIME_OF_ROUND');

        $round_no = Round::where('games_no', $this->game_no)->count();

        $round = new Round;
        $round->games_no = $this->game_no;
        $round->round = $round_no + 1;
        $round->state = gameSettings('STATE_RUNNING');
        $round->round_code = 0;
        $round->current_min = $this->current_min;
        $round->current_max = $this->current_max;
        $round->start_at = $start_timestamp;
        $round->end_at = $end_timestamp;
        $round->save();
    }

    /**
     * 該回合結束，根據該回合選號範圍產生回合密碼，寫入資料庫後，回傳回合密碼
     *
     * @return int
     */
    private function roundClosed()
    {
        $rand_min = $this->current_min;
        $rand_max = $this->current_max;

        $round_code = $this->getRandCode($rand_min, $rand_max);
        $round_no = Round::where('games_no', $this->game_no)->count();

        $round = Round
            ::where('games_no', $this->game_no)
            ->where('round', $round_no);
        $round->update([
            'state' => gameSettings('STATE_CLOSED'),
            'round_code' => $round_code
        ]);

        return $round_code;
    }

    /**
     * 初始化，關閉未關閉的期數，取得期數起始編號
     */
    private function init()
    {
        DB::table('games')
            ->where('state', '!=', gameSettings('STATE_CLOSED'))
            ->update(['state' => gameSettings('STATE_CLOSED')]);
        DB::table('rounds')
            ->where('state', '!=', gameSettings('STATE_CLOSED'))
            ->update(['state' => gameSettings('STATE_CLOSED')]);

        $this->game_date = Date("Ymd");
        $game = Game::where('no', 'like', $this->game_date . '%')->orderBy('no', 'desc');
        if ($game->count() > 0) {
            $maxIndex = ((int)str_replace($this->game_date, '', $game->first()->no)) + 1;
            $this->game_index = $maxIndex;
        }
    }

    /**
     * 一期遊戲的流程
     * 1.取得並更新設定檔至cache
     * 2.建立新的一期遊戲
     * 3.取得並設定選號範圍
     * 4.建立新的回合
     * 5.等待下注時間結束
     * 6.結束該回合
     * 7.結算該期遊戲該回合的單雙玩法注單
     * 8.判斷該回合密碼是否等於終極密碼->是：13/否：9
     * 9.依據回合密碼及終極密碼縮小選號範圍
     * 10.判斷選號範圍是否只剩下一個號碼->是：13/否：11
     * 11.判斷是否上有下一回合->是：4/否：12
     * 12.結束該期遊戲
     * 13.結算該期遊戲的選號玩法注單
     * 14.繼續執行本function->1
     */
    private function startServer()
    {
        updateSettings();
        $final_code = $this->gameStart();
        $this->echoGameData("Game Start");

        $this->current_min = gameSettings('CODE_RANGE_MIN');
        $this->current_max = gameSettings('CODE_RANGE_MAX');

        $round_count = 0;

        while ($round_count < gameSettings('ROUND_PER_GAME')) {

            $this->roundStart();
            $this->echoGameData("Round Start");

            sleep(gameSettings('TIME_OF_ROUND'));

            $round_code = $this->roundClosed();
            $this->echoGameData("Round Closed");

            $round_code_type = 1;
            if ($round_code % 2 == 0) {
                $round_code_type = 2;
            }

            App::make('App\Http\Controllers\HomeController')->billing($this->game_no, $round_count + 1,
                $round_code_type);

            if ($round_code == $final_code) {
                $this->memo = 'round code eq final code';
                break;
            }

            if ($round_code < $final_code) {
                $this->current_min = $round_code + 1;
            }

            if ($round_code > $final_code) {
                $this->current_max = $round_code - 1;
            }

            if ($this->current_max - $this->current_min <= 1) {
                $this->memo = 'current max and min code\'s range are left 1 number';
                break;
            }

            $round_count = Round::where('games_no', $this->game_no)->count();

            if ($round_count < gameSettings('ROUND_PER_GAME')) {
                sleep(gameSettings('ROUND_INTERVAL'));
            }
        }

        $this->gameClosed();
        $this->echoGameData("Game Closed");

        App::make('App\Http\Controllers\HomeController')->billing($this->game_no, 'all', $final_code);

        sleep(gameSettings('GAME_INTERVAL'));
        $this->startServer();
    }

    /**
     * 本排程起始執行function
     */
    public function run()
    {
        updateSettings();

        $this->init();

        $this->startServer();
    }

    /**
     * 於console中顯示目前遊戲資訊
     *
     * @param string $title　動作標題
     */
    private function echoGameData($title = "")
    {
        if (self::IS_DEBUG_MODE) {
            system('clear');
            print("$title " . Date("Y-m-d H:i:s") . " -------------\n");
            $game = Game::where('no', $this->game_no)->first();
            $rounds = Round::where('games_no', $this->game_no)->get();

            if ($game) {
                print("  game $game->no -------------\n");
                print("  final code: $game->final_code\n");
                print("  state:      $game->state\n");
                print("  start at:   $game->start_at\n");
                print("  memo:       $game->memo\n");
                print("  {\n");

                foreach ($rounds as $round) {
                    print("    Round $round->round -------------\n");
                    print("    round code:  $round->round_code\n");
                    print("    state:       $round->state\n");
                    print("    current min: $round->current_min\n");
                    print("    current max: $round->current_max\n");
                    print("    start at:    $round->start_at\n");
                    print("    end at:      $round->end_at\n");
                    print("    -------------------------------\n");
                }
                print("  }\n");
            }
            flush();
        } else {
            print(Date("Y-m-d H:i:s") . "  $title -------------\n");
        }
    }
}
