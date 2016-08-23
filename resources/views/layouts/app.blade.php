<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Final Code</title>
    <link rel="icon" href="favicon.png" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    <style>
        body {
            padding: 0px;
            margin: 0 auto;
            font-family: 'Lato';
        }
        .fa-btn {
            margin-right: 6px;
        }
        .btn-default:checked{
            background-color: red !important;
        }
        input[type=radio]{
            display: none;
        }
        .active{
            background-color: slategray !important;
            color: #FFFF00 !important;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('input[type=radio]').click(function(){
                var element_name = $(this).attr("name");
                var radioId = "num_" + $(this).val();

                //reset all radio (unchecked)
                $('input[name='+element_name+']').attr('checked', false);
                //reset all label (removeClass "active")
                $('#'+element_name+'Controller').find("label").removeClass("active");

                $('#' + radioId).attr('checked', true);
                $('#' + radioId).parent("label").addClass("active");
            });

            $('#btn_reset').click(function(){
                //reset all radio (unchecked)
                $('input[type=radio]').attr('checked', false);
                //reset all label (removeClass "active")
                $('div[id*=Controller]').find("label").removeClass("active");
            });
        });

    </script>
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

    </div>
</nav>

@yield('content')
</body>
</html>