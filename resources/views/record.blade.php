@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-offset-2 col-xs-8">
                <div class="row">
                    <div class="col-xs-2" style="padding-left: 0">
                        @if($last != 0)
                            <input id="input_games_no_last" type="hidden" value="{{$last}}">
                            <button id="btn_games_no_last" class="btn btn-success">
                                <i class="fa fa-fast-backward" style="margin-right: 10px;"></i>
                                上一期
                            </button>
                        @endif
                    </div>
                    <div class="col-xs-8" style="text-align: center">
                        <span style="font-size: large">第</span>
                        <input id="input_game_no_search" class="form-control" title="game_no" type="text"
                               value="{{$games_no}}" style="display:inline-block; width: 150px;text-align: center"/>
                        <span style="font-size: large">期</span>
                        <button id="btn_games_no_search" class="btn btn-success">
                            <i class="fa fa-search" style="margin-right: 10px;"></i>
                            查詢
                        </button>
                    </div>
                    <div class="col-xs-2" style="text-align: right; padding-right: 0">
                        @if($next != 0)
                            <input id="input_games_no_next" type="hidden" value="{{$next}}">
                            <button id="btn_games_no_next" class="btn btn-success">
                                下一期
                                <i class="fa fa-fast-forward" style="margin-left: 10px;"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            第&nbsp;&nbsp;&nbsp;<span id="gamesNo">{{$games_no}}</span>&nbsp;&nbsp;&nbsp;期
                        </div>

                        @if(is_null($game))
                            <div style="height: 200px; width: 100%; text-align: center; line-height: 200px; vertical-align: middle; color: red; font-size: x-large">{{$msg}}</div>
                        @else
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 panel panel-danger">
                                        <div class="panel-heading">
                                            終極密碼
                                        </div>

                                        <div class="panel-body">
                                            <div style="color: orangered; font-size: x-large; text-align: center">
                                                <label id="finalCode"
                                                       class="control-label">{{$game->final_code}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach($rounds as $round)
                                        <div id="round{{$round->round}}" class="col-xs-4 panel panel-success">
                                            <div class="panel-heading">
                                                第&nbsp;{{$round->round}}&nbsp;回合
                                            </div>

                                            <div class="panel-body">
                                                <div style="font-size:20px; width: 100%; text-align: center; color: orangered;">
                                                    <label id="roundCode"
                                                           class="control-label">{{$round->round_code}}</label>
                                                </div>
                                                <div class="roundTimes">
                                                    <div style="width: 100%; text-align: center">
                                                        <label id="startTime"
                                                               class="control-label">{{formatTimestamp($round->start_at)}}</label>
                                                    </div>
                                                    <div style="width: 100%; text-align: center">
                                                        <label class="control-label">|</label>
                                                    </div>
                                                    <div style="width: 100%; text-align: center">
                                                        <label id="endTime"
                                                               class="control-label">{{formatTimestamp($round->end_at)}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row" style="margin-top: 20px; border: 1px solid darkblue; border-radius: 4px; overflow: hidden;">
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center; background: #337ab7; color: white">下注總數</div>
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center" >{{$bet_detail_count}}</div>
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center; background: #337ab7; color: white">下注總金額</div>
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center" >{{$bet_total}}</div>
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center; background: #337ab7; color: white">獎金總金額</div>
                    <div class="col-xs-2" style="padding-top: 10px; padding-bottom: 10px; text-align: center" >{{$bet_win_total}}</div>
                </div>

                @if(!is_null($game))
                    <div class="row" style="margin-top: 20px; border: 1px solid darkblue; border-radius: 4px; overflow: hidden;">
                        @if(!is_null($bet_details) && sizeof($bet_details) > 0)
                            <div class="row bg-primary" style="padding-top: 10px; padding-bottom: 10px">
                                <div style="text-align: center" class="col-xs-1">回合</div>
                                <div style="text-align: center" class="col-xs-1">玩法</div>
                                <div style="text-align: center" class="col-xs-1">選擇</div>
                                <div style="text-align: center" class="col-xs-1">賠率</div>
                                <div style="text-align: center" class="col-xs-2">下注金額</div>
                                <div style="text-align: center" class="col-xs-2">獎金</div>
                                <div style="text-align: center" class="col-xs-4">下注時間</div>
                            </div>
                            <div style="max-height: 400px; overflow-x: hidden; overflow-y: auto;">
                            @foreach($bet_details as $bet)
                                <div class="row bet_datas" style="padding-top: 6px; padding-bottom: 4px; border-bottom: 1px solid lightslategray">
                                    <div style="text-align: center" class="col-xs-1">{{$bet->round}}</div>
                                    <div style="text-align: center" class="col-xs-1">{{formatPart($bet->part)}}</div>
                                    <div style="text-align: center" class="col-xs-1">{{formatGuess($bet->guess, $bet->part)}}</div>
                                    <div style="text-align: center" class="col-xs-1">{{$bet->odds}}</div>
                                    <div style="text-align: center" class="col-xs-2">{{$bet->bet}}</div>
                                    <div style="text-align: center" class="col-xs-2">{{$bet->win_cash}}</div>
                                    <div style="text-align: center" class="col-xs-4">{{formatTimestamp($bet->bet_at)}}</div>
                                </div>
                            @endforeach
                            </div>
                        @else
                            <div style="height: 200px; width: 100%; text-align: center; line-height: 200px; vertical-align: middle; color: red; font-size: x-large">
                                您未於該期遊戲中下注。
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection