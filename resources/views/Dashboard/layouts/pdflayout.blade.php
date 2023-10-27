<!DOCTYPE HTML>
<html>
    <head>
        <title>Submeter 4.0 | Home</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/Submeter_Favicon.ico')}}">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        
        <link href="{{asset('css/style.css?v=4.1.3')}}" rel='stylesheet' type='text/css' />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <link href="{{asset('css/custom.css?v=4.1.2')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.min.css" rel="stylesheet">
		<style type="text/css">
		  * {
              overflow: visible !important;
            }
		</style>  
    </head>
    <body class="bg-white">
    	@yield('content')    	 
    </body>    
</html>
