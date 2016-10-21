<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DateTime;
use App\Game;
use Illuminate\Http\Request;


class StatisticsController extends Controller
{

    public function index()
    {
        $view = view('statistics');
        $view->msg = session()->pull('msg', 'No message!');

        return $view;
    }

    public function finalCode(Request $request){
        $start_date = $request->input('startDate');
        $end_date = $request->input('endDate');
        $start_games_no = $request->input('startGameNo');
        $end_games_no = $request->input('endGameNo');

        if ($start_date === 'today') {
            $start_date = Date('Ymd');
        }
        if ($end_date === 'today') {
            $end_date = Date('Ymd');
        }

        if ($start_games_no === 'first') {
            $start_games_no = '0001';
        }
        if ($start_games_no === 'last') {
            $last_games_no = Game::getLastNoByDate($start_date);
            $start_games_no = substr($last_games_no, 8);
        }

        if ($end_games_no === 'first') {
            $end_games_no = '0001';
        }
        if ($end_games_no === 'last') {
            $last_games_no = Game::getLastNoByDate($end_date);
            $end_games_no = substr($last_games_no, 8);
        }

        if ($start_date !== 'all') {
            $start_date_games_no = $start_date . $start_games_no;
        } else {
            $start_date_games_no = $start_date;
        }

        if ($end_date !== 'all') {
            $end_date_games_no = $end_date . $end_games_no;
        } else {
            $end_date_games_no = $end_date;
        }

        $statistics_array = array_fill(1, 40, 0);
        $statistics = Game::statisticsFinalCode($start_date_games_no, $end_date_games_no);
        $dataArray = ["type" => 'column'];
        foreach ($statistics as $statistic) {
            $statistics_array[(int)$statistic['final_code']] = (int)$statistic['times'];
        }

        foreach ($statistics_array as $final_code => $times) {
            $data['label'] = $final_code;
            $data['y'] = (int)$times;
            $dataArray['dataPoints'][] = $data;
        }

        $current_date = Date('Ymd');
        if ($start_date !== 'all' || $end_date !== 'all') {
            $current_date = $start_date;
        }

        $date_obj = DateTime::createFromFormat('Ymd', $current_date);
        $date_obj->modify('-1 day');
        $last_date = $date_obj->format('Ymd');

        $date_obj = DateTime::createFromFormat('Ymd', $current_date);
        $date_obj->modify('+1 day');
        $next_date = $date_obj->format('Ymd');

        $datas['dataArray'] = json_encode($dataArray);
        $datas['no_min'] = Game::getMinClosedByColumnName('no', $start_date_games_no, $end_date_games_no);
        $datas['no_max'] = Game::getMaxClosedByColumnName('no', $start_date_games_no, $end_date_games_no);
        $datas['date_min'] = Game::getMinClosedByColumnName('start_at', $start_date_games_no, $end_date_games_no);
        $datas['date_max'] = Game::getMaxClosedByColumnName('start_at', $start_date_games_no, $end_date_games_no);
        $datas['no_total'] = Game::countOfClosedGames($start_date_games_no, $end_date_games_no);
        $datas['last_date'] = $last_date;
        $datas['next_date'] = $next_date;
        $datas['current_date'] = $current_date;

        return json_encode($datas);
    }


    public function dateList(){
        $date_list = Game::getDateList();

        $date_array = [];
        foreach($date_list as $date){
            $date_array[] = $date->date;
        }

        return json_encode($date_array);
    }


    public function gamesNoList(Request $request){
        $date = $request->input('date');

        $no_list = Game::getGamesNoList($date);

        $no_array = [];
        foreach($no_list as $no){
            $no_array[] = $no->no;
        }

        return json_encode($no_array);
    }
}
