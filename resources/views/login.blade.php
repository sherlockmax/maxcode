@extends('layouts.app')

@section('content')
    <div class="container">
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
                                    <label for="account" style="width: 150px"><i
                                                class="fa fa-btn fa-user"></i>Account</label>
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

                            @if ($errors->has('fail'))
                                <div class="fail">{{ $errors->first('fail') }}</div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection