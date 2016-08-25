@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Information
                </div>

                <div class="panel-body">

                    <div>
                        <label class="col-xs-5 control-label">No.</label>
                        <label class="control-label">201608220001</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">Create Time.</label>
                        <label class="control-label">{{Date("Y-m-d H:i:s")}}</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">Round.</label>
                        <label class="control-label">1/3</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">Start Time.</label>
                        <label class="control-label">2016-08-22 13:00:00</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">End Time.</label>
                        <label class="control-label">2016-08-22 13:01:00</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">Round Code.</label>
                        <label class="control-label">2/?/?</label>
                    </div>

                    <div>
                        <label class="col-xs-5 control-label">Final Code.</label>
                        <label class="control-label">20</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Status
                </div>

                <div class="panel-body">
                    <div style="text-align: center">Loading...</div>
                </div>
            </div>
        </div>

`       <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Playing
                </div>

                <div class="panel-body" style="text-align: center;">
                    <form action="{{ url('/bet') }}" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <div>
                                <label class="btn btn-default">
                                    <i class="fa fa-btn fa-dollar"></i>Bet：
                                    <input type="number" name="betAmount" id="betAmount" min="1000" max="10000" step="1000" value="1000">
                                    &nbsp;/&nbsp;<span id="maxPrice">10000</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label>Part 1：Try to guess Round Code is Odd or Even?</label>
                            </div>
                            <div id="numTypeController">
                                <label style="width: 200px; margin-right: 5px" class="btn btn-default">
                                    <input type="radio" name="numType" id="num_odd" value="odd"><i class="fa fa-btn fa-venus"></i>Odd
                                </label>
                                <label style="width: 200px" class="btn btn-default">
                                    <input type="radio" name="numType" id="num_even" value="even"><i class="fa fa-btn fa-venus-double"></i>Even
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label>Part 2：Try to guess Final Code</label>
                            </div>
                            <div id="numbersController">
                                @for ($i = 1; $i <= 40; $i++)
                                    <label style="width: 55px;margin-top: 3px" class="btn btn-default">
                                        <input type="radio" name="numbers" id="num_{{$i}}" value="{{$i}}">
                                        @if($i < 10)
                                            {{"0$i"}}
                                        @else
                                            {{$i}}
                                        @endif
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <button style="width: 200px; margin-right: 5px;" type="button" id="btn_reset" class="btn btn-warning">
                                <i class="fa fa-btn fa-refresh"></i>Clear Choose
                            </button>
                            <button style="width: 200px" type="submit" id="btn_reset" class="btn btn-success">
                                <i class="fa fa-btn fa-paper-plane"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Login
                </div>

                <div class="panel-body" style="text-align: center;">
                    <form action="{{ url('/login') }}" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <div>
                                <label>Log in to play...</label>
                            </div>
                            <div>
                                <label class="btn btn-default">
                                    <label for="account" style="width: 150px"><i class="fa fa-btn fa-user"></i>Account</label>
                                    <input id="account" type="text" name="account" value="">
                                </label>
                            </div>
                            <div>
                                <label class="btn btn-default">
                                    <label for="password" style="width: 150px"><i class="fa fa-btn fa-yelp"></i>Password</label>
                                    <input id="password" type="password" name="password" value="">
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button style="width: 200px; margin-right: 5px;" type="reset" class="btn btn-warning">
                                <i class="fa fa-btn fa-refresh"></i>Reset
                            </button>
                            <button style="width: 200px" type="submit" id="btn_reset" class="btn btn-success">
                                <i class="fa fa-btn fa-sign-in"></i>Log in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection