<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    //
    public static function get($key){
        return Settings::where('key', $key)->first()->value;
    }
}
