<!DOCTYPE HTML>
<html>
    <head>
        <title>Submeter 4.0 | Login</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/Submeter_Favicon.ico')}}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />        
        <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
        <link href="{{asset('css/bootstrap.min.css')}}" rel='stylesheet' type='text/css' />
        <!-- Custom Theme files -->
        <link href="{{asset('css/style.css?version=4.04')}}" rel='stylesheet' type='text/css' />
        <link href="{{asset('css/font-awesome.css')}}" rel="stylesheet"> 
        <script src="{{asset('js/jquery.min.js')}}"> </script>
        <script src="{{asset('js/bootstrap.min.js')}}"> </script>
        <link href="{{asset('plugins/glDatePicker/styles/glDatePicker.default.css')}}" rel="stylesheet"> 

    </head>
    <body  class="font_login"> 
        @yield('content')

        @yield('scripts')
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </body>
    <footer>
        <div class="copy-right">
            <p style="color: #E6E6E6"> &copy; 2020 Submeter 4.0. Todos los derechos reservados</p>     
        </div>
    </footer>
</html>