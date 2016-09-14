<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->string('games_no', 12)->comment = '遊戲期數';
            $table->integer('round')->comment = '回合數';
            $table->integer('round_code')->comment = '回合密碼';
            $table->integer('current_min')->comment = '該回合選號最小值';
            $table->integer('current_max')->comment = '該回合選號最大值';
            $table->integer('state')->comment = '0:執行中 / 1:結算中 / 2:已結束';
            $table->integer('start_at')->comment = '開始時間戳記';
            $table->integer('end_at')->comment = '結束時間戳記';

            $table->primary(['games_no', 'round']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rounds');
    }
}
