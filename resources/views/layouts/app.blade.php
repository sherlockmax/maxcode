<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Final Code</title>
    <link rel="icon" href="favicon.png" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="{{ URL::asset('js/main.js') }}"></script>
    @yield('gameScript')

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ URL::asset('css/main.css') }}" rel="stylesheet">
</head>
<body id="app-layout">
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                Final Code
            </a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
                <li><a href="javascript:void(0);"><i class="fa fa-btn fa-user"></i>{{ Auth::user()->name}}.</a></li>
                <li><a href="javascript:void(0);"><i class="fa fa-btn fa-dollar"></i><span id="userCash">{{ Auth::user()->cash}}</span></a></li>
                <li><a href="/logout"><i class="fa fa-btn fa-sign-out"></i>Log out</a></li>
            @else
                <li><a href="/login"><i class="fa fa-btn fa-sign-in"></i>Log in</a></li>
            @endif
        </ul>
    </div>
</nav>

@yield('content')
</body>
</html>