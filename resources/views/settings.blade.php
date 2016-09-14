@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    遊戲設定
                </div>

                <div class="panel-body" style="text-align: center;">
                    {{ Form::open(array('url' => '/settings')) }}
                    <div class="form-group row" style="margin-top: 5px;">
                        @foreach($settings as $setting)
                            <div>
                                <label class="btn btn-default col-xs-6 col-xs-offset-3" style="text-align: left">
                                    <div class="row">
                                        <i class="fa fa-btn fa-user col-xs-1"></i>
                                        {{Form::label($setting->key, $setting->key, array('class' => 'col-xs-5'))}}
                                        {{Form::text($setting->key, $setting->value, array('class' => 'col-xs-4'))}}
                                    </div>
                                    <div class="row"
                                         style="text-align: center; color: lightslategray">{{$setting->describe}}</div>
                                </label>
                            </div>
                        @endforeach
                        @if(count($errors) > 0)
                            <div>
                                <label class="alert alert-danger"
                                       style="padding: 5px; margin-top: 10px; margin-bottom: 0">
                                    {{ $errors->first() }}
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="form-group row">
                        <button style="width: 200px; margin-right: 5px;" type="reset" class="btn btn-warning">
                            <i class="fa fa-btn fa-refresh"></i>重新填寫
                        </button>
                        <button style="width: 200px" type="submit" id="btn_submit" class="btn btn-success">
                            <i class="fa fa-btn fa-user-plus"></i>完成修改
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection