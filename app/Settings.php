<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $guarded = ['id'];

    /**
     * 更新所有設定
     *
     * @param $inputs
     */
    public function updateAll($inputs)
    {
        foreach ($inputs as $key => $input) {
            Settings::where('key', $key)->update(['value' => $input]);
        }
    }
}
