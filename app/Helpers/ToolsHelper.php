<?php

if ( ! function_exists('floor_dec')) {
    function floor_dec($v, $precision){
        $c = pow(10, $precision);
        return fill_zero(floor($v*$c)/$c, 2);
    }
}

if ( ! function_exists('numTypeCount')) {
    function numTypeCount($min, $max){
        $counts = ['odd' => 0, 'even' => 0];
        for($i = $min; $i <= $max; $i++){
            if($i % 2 == 0){
                $counts['even']++;
            }else{
                $counts['odd']++;
            }
        }

        return $counts;
    }
}

if( ! function_exists('fill_zero')){
    function fill_zero($resource, $max){
        return sprintf('%01.'.$max.'f', $resource);
    }
}

if( ! function_exists('calcOdds')){
    function calcOdds($min, $max){
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

        $result['numbers'] = floor_dec(1 / $one_of_numbers_odd * 0.92, 2);
        $result['odd'] = fill_zero(1 / $one_of_odd_odd * config('gameset.STANDARD_ODDS'), 2);
        $result['even'] = fill_zero(1 / $one_of_even_odd * config('gameset.STANDARD_ODDS'), 2);

        return $result;
    }
}