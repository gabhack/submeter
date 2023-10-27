<!DOCTYPE HTML>
<html>
    <head>
			<title>Submeter 4.0 | Home</title>
			<link rel="shortcut icon" type="image/x-icon" href="{{asset('images/Submeter_Favicon.ico')}}">        
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<link href="{{asset('css/bootstrap.min.css')}}" rel='stylesheet' type='text/css' />        
			<link href="{{asset('css/style.css?v=4.0.4')}}" rel='stylesheet' type='text/css' />
			<link href="{{asset('css/font-awesome.css')}}" rel="stylesheet">
			<link href="{{asset('css/custom.css?v=4.0.5')}}" rel="stylesheet">
			<link rel="stylesheet" href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}">
			@yield('otherlinks')
			<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.min.css" rel="stylesheet">
			<!-- js necesario para las gráficas -->
			<script src="{{asset('js/Chart.js')}}"></script>            
		</head>
    <body>
			<nav class="navbar-default" role="navigation">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					{{-- @if(isset($ctrl) && $ctrl == 1)              
						<h1 class="fixed"> <a class="navbar-brand ellipsis" title="{!! $user->enterprise_name !!}" style="font-size: 20pt !important;">{!! $user->enterprise_name !!}</a></h1>
					@else
						<h1 class="fixed"> <a class="navbar-brand ellipsis" title="{!! $user->enterprise_name !!}" href="{!! route('home') !!}" style="font-size: 20pt !important;">{!! $user->enterprise_name !!}</a></h1>
					@endif --}}
					<a class="navbar-brand ellipsis fixed" style="padding: 0.4em 0;" href="{!! route('home') !!}"><img src="{{asset('images/submeter_logo_white_big.png')}}" alt="submeter_logo" width="150px" style="margin: 0 auto" /></a>
				</div>
				<div class="border-bottom">
					<div class="full-left">
						@if(isset($titulo))
							<section class="full-top" style="margin-left: inherit;">
								@if($titulo == 'Emisiones CO2')
									<h1 class="text-center title" style="font-size: 20pt; margin-left: 60px">Emisiones CO<sub>2</sub></h1>
								@else
									<h1 class="text-center title" style="font-size: 20pt; margin-left: 60px">{{$titulo}}</h1>
								@endif
							</section>
						@endif
						<div class="clearfix"> </div>
					</div>
				</div>            
        @include('Dashboard.includes.menu_horizontal')            
				<div class="clearfix"></div>      
				@include('Dashboard.includes.menu',array('user_log' => $user))                        
				<div class="clearfix"></div>
			</nav>                
			@yield('content')
			<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>				
			@yield('scripts')				
			<script type="text/javascript">
        $(document).ready(function(){    
					if (window.matchMedia('(max-width: 1279px)').matches) {                    
						// is mobile device
						//$(document).ready(function(){
							console.log('pantalla movil');
							//$('.collapsed').toggleClass('collapse');
							//$('.navbar-collapse').collapse('toggle');
							//$('.navbar-collapse').toggle();
							// $(".navbar-collapse").addClass('hide');
							//$('.collapsed').addClass('collapse');
							//$(".hvr-bounce-to-right").addClass('hide');
							//$(".navbar-collapse").collapse('hide');
							//$(".hvr-bounce-to-right").css("display","none !important");
							//$(".navbar-collapse").css("display","none !important");
							$("body").css("-moz-transform","scale(0.9, 0.9) !important");
							$("body").css("zoom","0.9 !important");
							$("body").css("zoom","90 !important");
							$("#exportButton").removeClass('pull-right');
							$("div").removeClass('pull-right');
							$("div").css("float","0 !important");
							$(".d-md-inline").css('display','inline !important');
							$("#button_optimizacion").removeClass('pull-left'); 
							$(".btn-primary").removeClass('float-left');
						//});
						var toogle_s = 0; // default
						localStorage.setItem('toogle_s', toogle_s);

						$(document).on('click','.navbar-toggle',function(){
							if(localStorage.getItem('toogle_s') == 0){
								console.log('expandir');
								var toogle_s = 1;
								localStorage.setItem('toogle_s', toogle_s);
								//$('.collapsed').removeClass('collapse');
								//$(".navbar-collapse").removeClass('hide');
								//$(".hvr-bounce-to-right").css("display","block !important");
							} else if (localStorage.getItem('toogle_s') == 1){
								console.log('replegar');
								var toogle_s = 0;
								localStorage.setItem('toogle_s', toogle_s);
								//$(".navbar-collapse").addClass('hide');
								//$('.collapsed').addClass('collapse');
								//$(".hvr-bounce-to-right").css("display","none !important");
							}
							//$(".hvr-bounce-to-right").css("display","block !important");
							console.log('click to toggle');                       
						});
					} else {
						console.log('pantalla grande')									
						//$(document).ready(function(){
							$(".hvr-bounce-to-right").css("display","none !important");
						//});
					}
        });        
			</script>
    </body>    
</html>
