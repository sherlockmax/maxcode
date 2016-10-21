<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsGrantColumnToBetDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bet_details', function (Blueprint $table) {
            $table->string('is_grant')->after('win_cash')->default(2)->comment = '0:未發放/1:已發放/2:無須發放';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bet_details', function (Blueprint $table) {
            $table->dropColumn('is_grant');
        });
    }
}
