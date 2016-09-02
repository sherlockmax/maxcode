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
            $table->string('user_id');
            $table->string('games_no');
            $table->integer('round');
            $table->integer('part')->comment = 'part number 1/2';
            $table->integer('guess');
            $table->integer('bet');
            $table->decimal('odds', 5, 2);
            $table->integer('win_cash')->default(0);
            $table->integer('bet_at');

            $table->unique(['games_no', 'round', 'user_id', 'part']);
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
