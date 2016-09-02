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
                            Game&nbsp;&nbsp;&nbsp;<span id="gamesNo">000000000000</span>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div id="round1" class="col-xs-4 panel panel-success">
                                    <div class="panel-heading">
                                        Round 1
                                    </div>

                                    <div class="panel-body">
                                        <div style="width: 100%; text-align: center">
                                            <label class="control-label">Round Code.</label>
                                        </div>
                                        <div style="font-size:20px; width: 100%; text-align: center; color: orangered;">
                                            <label id="roundCode" class="control-label">?</label>
                                        </div>
                                        <div class="roundTimes">
                                            <div style="width: 100%; text-align: center">
                                                <label id="startTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label class="control-label">|</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label id="endTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="round2" class="col-xs-4 panel panel-success">
                                    <div class="panel-heading">
                                        Round 2
                                    </div>

                                    <div class="panel-body">
                                        <div style="width: 100%; text-align: center">
                                            <label class="control-label">Round Code.</label>
                                        </div>
                                        <div style="font-size:20px; width: 100%; text-align: center; color: orangered;">
                                            <label id="roundCode" class="control-label">?</label>
                                        </div>
                                        <div class="roundTimes">
                                            <div style="width: 100%; text-align: center">
                                                <label id="startTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label class="control-label">|</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label id="endTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="round3" class="col-xs-4 panel panel-success">
                                    <div class="panel-heading">
                                        Round 3
                                    </div>

                                    <div class="panel-body">
                                        <div style="width: 100%; text-align: center">
                                            <label class="control-label">Round Code.</label>
                                        </div>
                                        <div style="font-size:20px; width: 100%; text-align: center; color: orangered;">
                                            <label id="roundCode" class="control-label">?</label>
                                        </div>
                                        <div class="roundTimes">
                                            <div style="width: 100%; text-align: center">
                                                <label id="startTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label class="control-label">|</label>
                                            </div>
                                            <div style="width: 100%; text-align: center">
                                                <label id="endTime" class="control-label">1911-01-01 00:00:00</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-8 panel panel-warning">
                                    <div class="panel-heading">
                                        Current state
                                    </div>

                                    <div class="panel-body">
                                        <div style="font-size: large; text-align: center">
                                            <label id="gameState" class="control-label">THE GAME IS NOT RUNNING</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4 panel panel-primary">
                                    <div class="panel-heading">
                                        Final code
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
                            Playing
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
                                        <label>Part 1：Try to guess Round Code is Odd or Even?</label>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <label class="btn btn-default">
                                            <i class="fa fa-btn fa-dollar"></i>Bet：
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
                                                <input type="radio" name="numType[]" id="numType_1" value="1"><i class="fa fa-btn fa-hand-pointer-o"></i>Odd
                                                &nbsp;&nbsp;<span id="odds_odd" style="font-size: 14px; color: palevioletred">?</span>
                                            </label>
                                            <label class="col-xs-4 btn btn-default">
                                                <input type="radio" name="numType[]" id="numType_2" value="2"><i class="fa fa-btn  fa-hand-peace-o"></i>Even
                                                &nbsp;&nbsp;<span id="odds_even" style="font-size: 14px; color: palevioletred">?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Part 2：Try to guess Final Code</label>
                                    </div>
                                    <div>
                                        Odds：<label id="odds_numbers" style="font-size: 14px; color: palevioletred">?</label>
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <label class="btn btn-default">
                                            <i class="fa fa-btn fa-dollar"></i>Bet：
                                            @if (Auth::user()->cash < 1000)
                                                <label style="color: darkred">Insufficient balance</label>
                                            @else
                                                <input type="number" name="bet_part2" min="0" step="1000" value="0">
                                            @endif
                                        </label>
                                    </div>
                                    <div id="numbersController">
                                        @for ($i = config('gameset.CODE_RANGE_MIN'); $i <= config('gameset.CODE_RANGE_MAX'); $i++)
                                            <label style="width: 55px;margin-top: 3px" class="btn btn-default">
                                                <input type="radio" name="numbers[]" id="num_{{$i}}" value="{{$i}}">
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
                                        <label style="color: orangered; font-size: large">Your cash must greater than $ 1000</label>
                                    @else
                                        <div class="col-xs-12 col-xs-offset-2">
                                            <button type="button" id="btn_reset" class="col-xs-4 btn btn-warning">
                                                <i class="fa fa-btn fa-refresh"></i>Reset
                                            </button>
                                            <button type="submit" id="btn_submit" class="col-xs-4 btn btn-success">
                                                <i class="fa fa-btn fa-paper-plane"></i>Submit
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
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        Message
                    </div>

                    <div class="panel-body">
                        {{$msg}}
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Your bet
                    </div>

                    <div id="bet_history_box" class="panel-body" style="margin-top: -20px">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bet_details_ex" style="display: none">
        <div class="row">
            <div class="col-xs-6" id="games_no">201600000001</div>
            <div class="col-xs-4" id="round">Round 0</div>
            <div class="col-xs-2" id="code">0</div>
        </div>
        <div class="row">
            <div style="text-align: center;" class="col-xs-4" id="guess"></div>
            <div class="col-xs-4" id="bet">$ </div>
            <div class="col-xs-4" id="win_cash">$ </div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
        </div>
    </div>
@endsection