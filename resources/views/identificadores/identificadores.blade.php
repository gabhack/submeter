@extends('Dashboard.layouts.global')

@section('content')
	<div id="wrapper">        
        <div id="page-wrapper" class="gray-bg dashbard-1">
       		<div class="content-main">	
       			@if($user->tipo == 2)       				
					<div class="banner col-md-12">							
					    <button id = "button_interval" type="button" class="btn btn-lg btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>  Intervalos de Períodos ({{$label_intervalo}})</button>					    	
					    @if(isset($ctrl) && $ctrl == 1)					    		
	  	     					<a href="{!! route('admin.users',[2, $id]) !!}" class="btn btn-info btn-lg float-right"><i class="fa fa-undo"></i></a>
	  	     			@endif
					</div>
					<div class="content-mid">					
						<div class="grid_3 col-md-12">	  	     				
	  	     				<div class="but_list">
	  	       					<div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
				   					<ul id="myTab" class="nav nav-tabs" role="tablist">
				   						@foreach($user->energy_meters as $i => $contador)
        		   							@if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, 16, $contador->id))
            		   							@if($contador->id == $user->current_count->meter_id)
                                					<li role="presentation" class="active">
                  			  							<a href="{{route('energymeter.change', [$user->id, $contador->id])}}" id="home-tab" style="font-size: 14pt"><i class="fa fa-clock-o"></i>{{$contador->count_label}}</a>
                  			  						</li>
              			  						@else
              			  							<li role="presentation">
                  			  							<a href="{{route('energymeter.change', [$user->id, $contador->id])}}" id="home-tab" style="font-size: 14pt"><i class="fa fa-clock-o"></i>{{$contador->count_label}}</a>
                  			  						</li>
              			  						@endif
              			  					@endif
                        				@endforeach
	  								</ul>
	  		  						<div id="myTabContent" class="tab-content">
	  		  							<div role="tabpanel" class="tab-pane fade in active col-md-12" id="Contador" aria-labelledby="Contador">
		  		  							<div class="col-md-12">
	  		  									<div class="alert alert-info text-center" role="alert" style="margin-top: 200px; margin-bottom: 300px">
												  <strong>Actualmente no tiene equipos monitorizados en este espacio</strong>
												</div>
	  		  								</div>
  		  								</div>
	  		  						</div>
	       						</div>
	       					</div>
	      				</div>
					</div>
					<div class="clearfix"> </div>
				@else
					<div class="banner col-md-12">
						<br><br>
					</div>

					<div class="content-mid">
						<div class="grid_3 col-md-12">				    	
					    	<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
					    </div>
					</div>
					<div class="clearfix"></div>
				@endif
			</div>
			<div class="content-bottom">				
          		@include('Dashboard.modals.modal_intervalos');
			</div>
			<div class="copy">
	            <p> &copy; 2020 Submeter 4.0. Todos los derechos reservados</p>
			</div>
		</div>
		<div class="clearfix"> </div>
	</div>
@endsection

@section('scripts')
	<script src="{{asset('js/jquery.min.js')}}"> </script>
	<script src="{{asset('js/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('js/jquery.slimscroll.min.js')}}"></script>        
    <script src="{{asset('js/custom.js')}}"></script>
    <script src="{{asset('js/screenfull.js')}}"></script>
	<script src="{{asset('js/scripts.js')}}"></script>
    <script src="{{asset('js/jquery.nicescroll.js')}}"></script>	
	<script src="{{asset('js/bootstrap.min.js')}}"> </script>
    <script src="{{asset('js/pie-chart.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/skycons.js')}}"></script>
@endsection