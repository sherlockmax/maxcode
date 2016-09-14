<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no', 12)->comment = '遊戲期數';
            $table->integer('final_code')->comment = '終極密碼';
            $table->integer('state')->comment = '0:執行中 / 1:結算中 / 2:已結束';
            $table->integer('start_at')->comment = '遊戲開始時間戳記';
            $table->string('memo')->nullable()->comment = '結束原因,小記';

            $table->unique('no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('games');
    }
}
