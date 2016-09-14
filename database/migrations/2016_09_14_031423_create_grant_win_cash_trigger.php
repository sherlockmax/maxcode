<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrantWinCashTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER grant_win_cash AFTER UPDATE ON `bet_details` FOR EACH ROW
            BEGIN
                UPDATE `users` SET `cash` = `cash` + NEW.win_cash WHERE OLD.user_id = id AND OLD.is_grant = 0 AND NEW.win_cash > 0;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `grant_win_cash`');
    }
}
