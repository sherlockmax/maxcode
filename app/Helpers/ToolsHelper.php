<?php

if (!function_exists('floor_dec')) {
    /**
     *　格式化小數點後的位數
     *
     * @param $value　欲格式化的值
     * @param $precision 小數點位數
     * @return string
     */
    function floor_dec($value, $precision)
    {
        $c = pow(10, $precision);
        return fill_zero(floor($value * $c) / $c, $precision);
    }
}

if (!function_exists('numTypeCount')) {
    /**
     * 計算出範圍中單數與雙數的個數
     *
     * @param $min　範圍最小值
     * @param $max　範圍最大值
     * @return array　key=['odd', 'even']
     */
    function numTypeCount($min, $max)
    {
        $counts = ['odd' => 0, 'even' => 0];
        for ($i = $min; $i <= $max; $i++) {
            if ($i % 2 == 0) {
                $counts['even']++;
            } else {
                $counts['odd']++;
            }
        }

        return $counts;
    }
}

if (!function_exists('fill_zero')) {
    function fill_zero($resource, $max)
    {
        return sprintf('%01.' . $max . 'f', $resource);
    }
}

if (!function_exists('calcOdds')) {
    /**
     * 計算出指定範圍內單雙/選號玩法中，各個選擇的賠率
     *
     * @param $min 範圍最小值
     * @param $max　範圍最大值
     * @return array　key=['numbers', 'odd', 'even']
     */
    function calcOdds($min, $max)
    {
        $result = [
            'numbers' => floor_dec(0.0, 2),
            'odd' => floor_dec(0.0, 2),
            'even' => floor_dec(0.0, 2),
        ];

        $numTypeCounts = numTypeCount($min, $max);
        $numbers_count = ($max - $min) + 1;
        $one_of_numbers_odd = 1 / $numbers_count;

        $one_of_odd_odd = $numTypeCounts['odd'] / $numbers_count;
        $one_of_even_odd = $numTypeCounts['even'] / $numbers_count;

        $result['numbers'] = floor_dec(1 / $one_of_numbers_odd * gameSettings('STANDARD_ODDS'), 2);
        $result['odd'] = floor_dec(1 / $one_of_odd_odd * gameSettings('STANDARD_ODDS'), 2);
        $result['even'] = floor_dec(1 / $one_of_even_odd * gameSettings('STANDARD_ODDS'), 2);

        return $result;
    }
}

if (!function_exists('formatTimestamp')) {
    /**
     * 格式化時間戳記為Y-m-d H:i:s
     *
     * @param $timestamp 時間戳記
     * @return string
     */
    function formatTimestamp($timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date->format('Y-m-d H:i:s');
    }
}

if (!function_exists('formatGuess')) {
    /**
     * 依據玩法格式化下注選擇至顯示頁面
     *
     * @param $guess　下注選擇
     * @param $part　玩法
     * @return string
     */
    function formatGuess($guess, $part)
    {
        if ($part == 1) {
            if ($guess % 2) {
                return "雙";
            } else {
                return "單";
            }
        } else {
            return $guess;
        }
    }
}

if (!function_exists('formatPart')) {
    /**
     * 格式化玩法至顯示頁面
     *
     * @param $part　玩法
     * @return string
     */
    function formatPart($part)
    {
        if ($part == 1) {
            return "單雙";
        } else {
            return "選號";
        }
    }
}

if (!function_exists('updateSettings')) {
    /**
     * 取得資料庫中的遊戲設定至ｃache，並判斷是否有無變更
     */
    function updateSettings()
    {
        $is_changed = false;
        $settings_all = App\Settings::All();
        foreach ($settings_all as $setting) {
            $update_time_key = $setting->key . '_update';
            if (Redis::get($update_time_key) != $setting->updated_at) {
                Redis::set($setting->key, $setting->value);
                Redis::set($update_time_key, $setting->updated_at);
                $is_changed = true;
            }
        }

        if ($is_changed) {
            Redis::set('is_setting_changed', 'true');
        } else {
            Redis::set('is_setting_changed', 'false');
        }
    }
}

if (!function_exists('gameSettings')) {
    /**
     * 至ｃache中取得遊戲設定,若無法於chache中取得,則至config設定檔中取得
     *
     * @param $key　設定名稱
     * @return bool|mixed|string
     */
    function gameSettings($key)
    {
        try {
            return Redis::get($key);
        } catch (\Mockery\CountValidator\Exception $e) {
            return config('gameset.' . $key);
        }
    }
}