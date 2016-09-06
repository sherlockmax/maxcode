@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-offset-2 col-xs-8">
                <div class="row">
                    <div class="col-xs-2" style="padding-left: 0">
                        <button class="btn btn-success">
                            <i class="fa fa-fast-backward" style="margin-right: 10px;"></i>
                            上一期
                        </button>
                    </div>
                    <div class="col-xs-8" style="text-align: center">
                        <div class="col-xs-offset-2 col-xs-4" style="text-align: right; padding-right: 5px;">
                            <input class="form-control" title="game_no" type="text" value="{{$games_no}}" style="text-align: center"/>
                        </div>
                        <div class="col-xs-4" style="text-align: left; padding-left: 5px;">
                            <button class="btn btn-success col-xs-12">
                                <i class="fa fa-search" style="margin-right: 10px;"></i>
                                查詢
                            </button>
                        </div>
                    </div>
                    <div class="col-xs-2" style="text-align: right; padding-right: 0">
                        <button class="btn btn-success">
                            下一期
                            <i class="fa fa-fast-forward" style="margin-left: 10px;"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            第&nbsp;&nbsp;&nbsp;<span id="gamesNo">{{$games_no}}</span>&nbsp;&nbsp;&nbsp;期
                        </div>


                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12 panel panel-danger">
                                    <div class="panel-heading">
                                        終極密碼
                                    </div>

                                    <div class="panel-body">
                                        <div style="color: orangered; font-size: large; text-align: center">
                                            <label id="finalCode" class="control-label">?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @for($i = 1; $i <= config('gameset.ROUND_PER_GAME'); $i++)
                                    <div id="round{{$i}}" class="col-xs-4 panel panel-success">
                                        <div class="panel-heading">
                                            第&nbsp;{{$i}}&nbsp;回合
                                        </div>

                                        <div class="panel-body">
                                            <div style="font-size:20px; width: 100%; text-align: center; color: orangered;">
                                                <label id="roundCode" class="control-label">?</label>
                                            </div>
                                            <div class="roundTimes">
                                                <div style="width: 100%; text-align: center">
                                                    <label id="startTime" class="control-label">1911-01-01
                                                        00:00:00</label>
                                                </div>
                                                <div style="width: 100%; text-align: center">
                                                    <label class="control-label">|</label>
                                                </div>
                                                <div style="width: 100%; text-align: center">
                                                    <label id="endTime" class="control-label">1911-01-01
                                                        00:00:00</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection