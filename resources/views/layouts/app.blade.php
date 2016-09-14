<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>邁克斯密碼</title>
    <link rel="icon" href="favicon.png" type="image/x-icon"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="{{ URL::asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::asset('js/main.js') }}"></script>
    @yield('gameScript')

            <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet'
          type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ URL::asset('css/main.css') }}" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css"
          rel="stylesheet" type="text/css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    @if(isset($msg) && $msg != 'No message!')
        <script>
            $(document).ready(function () {
                showMsg('{{$msg}}');
            });
        </script>
    @endif
</head>
<body id="app-layout">
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                邁克斯密碼
            </a>
        </div>
        <ul class="nav navbar-nav navbar-left">
            <li><a href="{{ url('/') }}"><i class="fa fa-btn fa-gamepad"></i>遊戲大廳</a></li>
            <li><a href="{{ url('/record') }}"><i class="fa fa-btn fa-history"></i>注單歷史</a></li>
            <li><a href="javascript:void(0);"><i class="fa fa-btn fa-question-circle"></i>玩法介紹</a></li>
            @if(Auth::check())
                @if(Auth::user()->account == 'max')
                    <li><a href="{{ url('/settings') }}"><i class="fa fa-btn fa-question-circle"></i>遊戲設定</a></li>
                @endif
            @endif
        </ul>
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
                <li><a href="javascript:void(0);"><i class="fa fa-btn fa-user"></i>{{ Auth::user()->name}}.</a></li>
                <li><a href="javascript:void(0);"><i class="fa fa-btn fa-dollar"></i><span
                                id="userCash">{{ Auth::user()->cash}}</span></a></li>
                <li><a href="/logout"><i class="fa fa-btn fa-sign-out"></i>不賭了</a></li>
            @else
                <li><a href="/signup"><i class="fa fa-btn  fa-user-plus"></i>成為會員</a></li>
            @endif
        </ul>
    </div>
</nav>

@yield('content')
</body>
</html>