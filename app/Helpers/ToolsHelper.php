<?php

if (!function_exists('floor_dec')) {
    function floor_dec($v, $precision)
    {
        $c = pow(10, $precision);
        return fill_zero(floor($v * $c) / $c, 2);
    }
}

if (!function_exists('numTypeCount')) {
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
    function calcOdds($min, $max)
    {
        $result = [
            'numbers' => fill_zero(0.0, 2),
            'odd' => fill_zero(0.0, 2),
            'even' => fill_zero(0.0, 2),
        ];

        $numTypeCounts = numTypeCount($min, $max);
        $numbers_count = ($max - $min) + 1;
        $one_of_numbers_odd = floor_dec(1 / $numbers_count, 2);

        $one_of_odd_odd = floor_dec($numTypeCounts['odd'] / $numbers_count, 2);
        $one_of_even_odd = floor_dec($numTypeCounts['even'] / $numbers_count, 2);

        $result['numbers'] = floor_dec(1 / $one_of_numbers_odd * gameSettings('STANDARD_ODDS'), 2);
        $result['odd'] = fill_zero(1 / $one_of_odd_odd * gameSettings('STANDARD_ODDS'), 2);
        $result['even'] = fill_zero(1 / $one_of_even_odd * gameSettings('STANDARD_ODDS'), 2);

        return $result;
    }
}

if (!function_exists('formatTimestamp')) {
    function formatTimestamp($timestamp)
    {
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        return $date->format('Y-m-d H:i:s');
    }
}

if (!function_exists('formatGuess')) {
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
    function updateSettings()
    {
        $settings_all = App\Settings::All();

        foreach($settings_all as $setting){
            Redis::set($setting->key, $setting->value);
        }
    }
}

if (!function_exists('gameSettings')) {
    function gameSettings($key)
    {
        try {
            return Redis::get($key);
        }catch (\Mockery\CountValidator\Exception $e){
            return config('gameset.'.$key);
        }
    }
}