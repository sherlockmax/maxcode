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
            $table->string('games_no', 12);
            $table->integer('round');
            $table->integer('round_code');
            $table->integer('current_min');
            $table->integer('current_max');
            $table->integer('state')->comment = '0:running / 1:closing / 2:closed';
            $table->integer('start_at');
            $table->integer('end_at');

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
