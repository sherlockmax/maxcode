@extends('layouts.app')

@section('gameScript')
    <script src="{{ URL::asset('js/game.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-offset-1 col-xs-7">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            第&nbsp;&nbsp;&nbsp;<span id="gamesNo">000000000000</span>&nbsp;&nbsp;&nbsp;期
                        </div>


                        <div class="panel-body">
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

                            <div class="row">
                                <div class="col-xs-8 panel panel-warning">
                                    <div class="panel-heading">
                                        目前狀態
                                    </div>

                                    <div class="panel-body">
                                        <div style="font-size: large; text-align: center">
                                            <label id="gameState" class="control-label">維護中，暫不提供服務。</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 panel panel-primary">
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
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            下注表單
                        </div>

                        <div class="panel-body" style="text-align: center;">
                            <form action="{{ url('/bet') }}" method="POST" class="form-horizontal">
                                <input type="hidden" name="games_no" value="000000000000">
                                <input type="hidden" name="round_no" value="0">
                                <input type="hidden" name="odds_odd" value="0">
                                <input type="hidden" name="odds_even" value="0">
                                <input type="hidden" name="odds_numbers" value="0">
                                <div class="form-group">
                                    <div>
                                        <label>玩法 1：選擇回合密碼為單或為雙?</label>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <label class="btn btn-default">
                                            <i class="fa fa-btn fa-dollar"></i>下注金額：
                                            @if (Auth::user()->cash < 1000)
                                                <label style="color: darkred">Insufficient balance</label>
                                            @else
                                                <input type="number" name="bet_part1" min="0" step="1000" value="0">
                                            @endif
                                        </label>
                                    </div>
                                    <div id="numTypeController" class="row">
                                        <div class="col-xs-12 col-xs-offset-2">
                                            <label class="col-xs-4 btn btn-default">
                                                <input type="checkbox" name="numType[]" id="numType_1" value="1"><i
                                                        class="fa fa-btn fa-hand-pointer-o"></i>單
                                                &nbsp;&nbsp;<span id="odds_odd"
                                                                  style="font-size: 14px; color: palevioletred">?</span>
                                            </label>
                                            <label class="col-xs-4 btn btn-default">
                                                <input type="checkbox" name="numType[]" id="numType_2" value="2"><i
                                                        class="fa fa-btn  fa-hand-peace-o"></i>雙
                                                &nbsp;&nbsp;<span id="odds_even"
                                                                  style="font-size: 14px; color: palevioletred">?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>玩法 2：選擇終極密碼&nbsp;&nbsp;&nbsp;(賠率：<label id="odds_numbers"
                                                                                       style="font-size: 14px; color: palevioletred">?</label>)</label>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <label class="btn btn-default">
                                            <i class="fa fa-btn fa-dollar"></i>下注金額：
                                            @if (Auth::user()->cash < 1000)
                                                <label style="color: darkred">所擁有可下注金額不足</label>
                                            @else
                                                <input type="number" name="bet_part2" min="0" step="1000" value="0">
                                            @endif
                                        </label>
                                    </div>
                                    <div id="numbersController">
                                        @for ($i = config('gameset.CODE_RANGE_MIN'); $i <= config('gameset.CODE_RANGE_MAX'); $i++)
                                            <label style="width: 55px;margin-top: 3px" class="btn btn-default">
                                                <input type="checkbox" name="numbers[]" id="num_{{$i}}" value="{{$i}}">
                                                @if($i < 10)
                                                    {{"0$i"}}
                                                @else
                                                    {{$i}}
                                                @endif
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="row form-group">
                                    @if (Auth::user()->cash < 1000)
                                        <label style="color: orangered; font-size: large">Your cash must greater than $
                                            1000</label>
                                    @else
                                        <div class="col-xs-12 col-xs-offset-2">
                                            <button type="button" id="btn_reset" class="col-xs-4 btn btn-warning">
                                                <i class="fa fa-btn fa-refresh"></i>清除選擇
                                            </button>
                                            <button type="submit" id="btn_submit" class="col-xs-4 btn btn-success">
                                                <i class="fa fa-btn fa-paper-plane"></i>確認下注
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-3">
                <div id="bet_history_part_1" class="panel panel-primary">
                    <div class="panel-heading">
                        本期下注資訊&nbsp;-&nbsp;玩法&nbsp;1
                    </div>

                    <div id="bet_history_box" class="panel-body" style="max-height: 355px; overflow: auto">
                        您尚未對本期遊戲進行下注
                    </div>
                </div>

                <div id="bet_history_part_2" class="panel panel-primary">
                    <div class="panel-heading">
                        本期下注資訊&nbsp;-&nbsp;玩法&nbsp;2
                    </div>

                    <div id="bet_history_box" class="panel-body" style="max-height: 355px; overflow: auto">
                        您尚未對本期遊戲進行下注
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bet_details_ex" style="display: none">
        <div class="row">
            <div style="text-align: center;" class="col-xs-6">
                第&nbsp;<span id="round"></span>&nbsp;回合
            </div>
            <div style="text-align: center;" class="col-xs-6" id="odds"></div>
        </div>
        <div class="row">
            <div style="text-align: center; color:blue;" class="col-xs-6" id="guess"></div>
            <div style="text-align: center;" class="col-xs-6" id="code"></div>
        </div>
        <div class="row">
            <div style="text-align: center; color:blue;" class="col-xs-6" id="bet"></div>
            <div style="text-align: center;" class="col-xs-6" id="win_cash"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
        </div>
    </div>

    <div id="big_winner_card" style="display: none">
        <div id="content">
            <div>恭喜玩家：<span id="big_winner_name">XXX</span></div>
            <div>於第 <span id="big_winner_no">200000000002</span> 期</div>
            <div>成為最大贏家</div>
            <div>共獲得獎金 <span id="big_winner_win_cash">0</span> 元</div>
        </div>
        <div id="buttonBox">
            <button id="btn_close_big_winner_card" class="btn btn-default">關閉視窗</button>
        </div>
    </div>
@endsection