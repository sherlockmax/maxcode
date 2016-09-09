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
                                @for($i = 1; $i <= gameSettings('ROUND_PER_GAME'); $i++)
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
                                <div class="form-group">
                                    <div>
                                        <label>玩法 1：選擇回合密碼為單或為雙?</label>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <label class="btn btn-default">
                                            <div id="show_keyboard_1" class="row" style="padding-left: 15px; padding-right: 10px">
                                                <i class="fa fa-btn fa-dollar"></i>下注金額：
                                                @if (Auth::user()->cash < 1000)
                                                    <label style="color: darkred">所擁有可下注金額不足</label>
                                                @else
                                                    <input title="" type="text" name="bet_part1" value="0" style="text-align: center;" readonly>
                                                @endif
                                                <i class="fa fa-btn fa-keyboard-o" style="margin-left: 15px;"></i>
                                            </div>
                                            <div class="row">
                                                <div id="money_keyboard_1" class="col-xs-offset-1 col-xs-10">
                                                    <div class="row" style="margin-top: 15px">
                                                        <button type="button" class="btn btn-default col-xs-4">7</button>
                                                        <button type="button" class="btn btn-default col-xs-4">8</button>
                                                        <button type="button" class="btn btn-default col-xs-4">9</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">4</button>
                                                        <button type="button" class="btn btn-default col-xs-4">5</button>
                                                        <button type="button" class="btn btn-default col-xs-4">6</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">1</button>
                                                        <button type="button" class="btn btn-default col-xs-4">2</button>
                                                        <button type="button" class="btn btn-default col-xs-4">3</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">Del</button>
                                                        <button type="button" class="btn btn-default col-xs-4">0</button>
                                                        <button type="button" class="btn btn-default col-xs-4">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="numTypeController" class="row">
                                        <div class="col-xs-12 col-xs-offset-2">
                                            <label class="col-xs-4 btn btn-default" style="margin-left:-5px">
                                                <input type="checkbox" name="numType[]" id="numType_1" value="1"><i
                                                        class="fa fa-btn fa-hand-pointer-o"></i>單
                                                &nbsp;&nbsp;<span id="odds_odd"
                                                                  style="font-size: 14px; color: palevioletred">?</span>
                                            </label>
                                            <label class="col-xs-4 btn btn-default" style="margin-left:5px">
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
                                            <div id="show_keyboard_2" class="row" style="padding-left: 15px; padding-right: 10px">
                                                <i class="fa fa-btn fa-dollar"></i>下注金額：
                                                @if (Auth::user()->cash < 1000)
                                                    <label style="color: darkred">所擁有可下注金額不足</label>
                                                @else
                                                    <input title="" type="text" name="bet_part2" value="0" style="text-align: center;" readonly>
                                                @endif
                                                <i class="fa fa-btn fa-keyboard-o" style="margin-left: 15px;"></i>
                                            </div>
                                            <div class="row">
                                                <div id="money_keyboard_2" class="col-xs-offset-1 col-xs-10">
                                                    <div class="row" style="margin-top: 15px">
                                                        <button type="button" class="btn btn-default col-xs-4">7</button>
                                                        <button type="button" class="btn btn-default col-xs-4">8</button>
                                                        <button type="button" class="btn btn-default col-xs-4">9</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">4</button>
                                                        <button type="button" class="btn btn-default col-xs-4">5</button>
                                                        <button type="button" class="btn btn-default col-xs-4">6</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">1</button>
                                                        <button type="button" class="btn btn-default col-xs-4">2</button>
                                                        <button type="button" class="btn btn-default col-xs-4">3</button>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" class="btn btn-default col-xs-4">Del</button>
                                                        <button type="button" class="btn btn-default col-xs-4">0</button>
                                                        <button type="button" class="btn btn-default col-xs-4">OK</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div id="numbersController">
                                        @for ($i = gameSettings('CODE_RANGE_MIN'); $i <= gameSettings('CODE_RANGE_MAX'); $i++)
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
                                        <label style="color: orangered; font-size: large">請確認所用有的金額是否可下注</label>
                                    @else
                                        <div class="col-xs-12 col-xs-offset-2">
                                            <button type="button" id="btn_reset" class="col-xs-3 btn btn-warning" style="margin-left:-10px">
                                                <i class="fa fa-btn fa-refresh"></i>清除選擇
                                            </button>
                                            <button type="button" id="choose_all" class="col-xs-2 btn btn-info" style="margin-left:5px">
                                                選號全選
                                            </button>
                                            <button type="submit" id="btn_submit" class="col-xs-3 btn btn-success" style="margin-left:5px">
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
                        本期下注資訊&nbsp;-&nbsp;單雙
                    </div>

                    <div id="bet_history_box" class="panel-body" style="max-height: 355px; overflow: auto">
                        您尚未對本期遊戲進行下注
                    </div>
                </div>

                <div id="bet_history_part_2" class="panel panel-primary">
                    <div class="panel-heading">
                        本期下注資訊&nbsp;-&nbsp;選號
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