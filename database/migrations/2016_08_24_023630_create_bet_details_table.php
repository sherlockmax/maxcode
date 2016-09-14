<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->comment = '下注者ID';
            $table->string('games_no')->comment = '遊戲期數';
            $table->integer('round')->comment = '回合數';
            $table->integer('part')->comment = '玩法（1：單雙/2：選號）';
            $table->integer('guess')->comment = '下注者的選擇';
            $table->integer('bet')->comment = '下注金額';
            $table->decimal('odds', 5, 2)->comment = '該單賠率';
            $table->integer('win_cash')->default(0)->comment = '獲獎金額（=0：待結算/<0：輸/>0:贏）';
            $table->integer('bet_at')->comment = '下注時間戳記';

            $table->index(['games_no', 'round']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bet_details');
    }
}
