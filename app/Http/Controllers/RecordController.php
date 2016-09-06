<?php

namespace App\Http\Controllers;

use Auth;

class RecordController extends Controller
{
    public function index(){
        return $this->record('201608090009');
    }

    public function record($games_no)
    {
        $view = view('record');
        $view->games_no = $games_no;

        return $view;
    }
}
