@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    註冊表單
                </div>

                <div class="panel-body" style="text-align: center;">
                    {{ Form::open(array('url' => '/signup')) }}
                        <div class="form-group">
                            <div>
                                <label class="btn btn-default">
                                    <i class="fa fa-btn fa-user" style="margin-left: 20px"></i>
                                    {{Form::label('account', '帳　　號', array('style' => 'margin-right: 30px'))}}
                                    {{Form::text('account')}}
                                </label>
                            </div>
                            <div>
                                <label class="btn btn-default">
                                    <i class="fa fa-btn fa-font" style="margin-left: 20px"></i>
                                    {{Form::label('name', '暱　　稱', array('style' => 'margin-right: 30px'))}}
                                    {{Form::text('name')}}
                                </label>
                            </div>
                            <div>
                                <label class="btn btn-default">
                                    <i class="fa fa-btn fa-yelp" style="margin-left: 20px"></i>
                                    {{Form::label('password', '密　　碼', array('style' => 'margin-right: 30px'))}}
                                    {{Form::password('password')}}
                                </label>
                            </div>
                            <div>
                                <label class="btn btn-default">
                                    <i class="fa fa-btn fa-yelp" style="margin-left: 20px"></i>
                                    {{Form::label('password_check', '密碼確認', array('style' => 'margin-right: 30px'))}}
                                    {{Form::password('password_check')}}
                                </label>
                            </div>
                            @if(count($errors) > 0)
                                <div>
                                    <label class="alert alert-danger" style="padding: 5px; margin-top: 10px; margin-bottom: 0">
                                        {{ $errors->first() }}
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <button style="width: 200px; margin-right: 5px;" type="reset" class="btn btn-warning">
                                <i class="fa fa-btn fa-refresh"></i>重新填寫
                            </button>
                            <button style="width: 200px" type="submit" id="btn_reset" class="btn btn-success">
                                <i class="fa fa-btn fa-user-plus"></i>成為會員
                            </button>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>


    </div>
@endsection