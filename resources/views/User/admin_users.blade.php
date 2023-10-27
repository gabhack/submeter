@extends('Dashboard.layouts.global')

@section('content')
	<div id="wrapper">        
        <div id="page-wrapper" class="gray-bg dashbard-1">        	
       		<div class="content-main">	       			
				<div class="banner col-md-12">
					@if($id == 1)
				    	<a href="{{ route('create.users') }}" class="btn btn-lg btn-primary"><i class="fa fa-plus"></i>  Registrar Administrador</a>
				    @else
				    	<a href="{{ route('create.cliente') }}" class="btn btn-lg btn-primary"><i class="fa fa-plus"></i>  Registrar Clientes</a>
				    @endif
				</div>
				<div class="content-mid" >					
					<div class="grid_3 col-md-12" style="height: 500%;">
						@if($id == 1)
  	     					<h3 class="head-top">Administración de Super-Usuarios</h3>
  	     				@else
  	     					<h3 class="head-top">Administración de Clientes</h3> 
  	     				@endif
  	     				@if (Session::has('message') && !is_array(Session::get('message')))
			                <div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			            @endif
			            @if ( Session::get('message-error') )
	                        <div id="message-danger" class="alert alert-danger">{{ Session::get('message-error') }}</div>
	                    @endif
  	     				<table class="table table-responsive" id="tabla2" style="width: 100%;">
							<thead>										
								<tr>
									<th class="text-center">Id</th>
									<th class="text-center">Activo</th>											
									<th class="text-center">Nombre</th>											
									<th class="text-center">Apellido</th>										
									<th class="text-center">Dirección</th>
									<th class="text-center">Correo</th>
									<th class="text-center">Organización</th>											
									<th class="text-center">Total Contadores</th>
									<th class="text-center">Acciones</th>
								</tr>
							</thead>									
							<tbody>
								@if(!empty($users))
									@foreach($users as $cliente)
										@if($cliente->id != $user->id)
											<?php 
												if ($cliente->status == 1) {
													$estatus = "Si";
												}else{
													$estatus = "No";
												}
											 ?>
											<tr>
												<td class="text-center">{{$cliente->id}}</td>
												<td class="text-center">{{$estatus}}</td>
												@if(isset($cliente->_perfil))
													<td class="text-center">{{$cliente->_perfil->nombre}}</td>
													<td class="text-center">{{$cliente->_perfil->apellido}}</td>
													<td class="text-center">{{$cliente->_perfil->direccion}}</td>
												@else
													<td class="text-center">Sin nombre</td>
													<td class="text-center">Sin apellido</td>
													<td class="text-center">Sin dirección</td>
												@endif
												<td class="text-center">{{$cliente->email}}</td>
												<td class="text-center">{{$cliente->name}}</td>
												<td class="text-center">{{count($cliente->_count)}}</td>
												<td class="text-center">											
													<div class="d-inline">
														@if($cliente->tipo == 2)
															<a href="{{ route('resumen.energia.potencia',$cliente->id) }}" name="ver" class="btn btn-info btn-accion-user"><i class="fa fa-eye"></i></a>														
														@endif
														<a href="{{ route('edit.user', $cliente->id) }}" name="editar" class="btn btn-info btn-accion-user btn-circle"><i class="fa fa-edit"></i></a>
														<a name="eliminar" class="btn btn-info btn-accion-user margin-left-8 btn-circle" data-toggle="modal" data-target="#delete_client_modal" onclick="deleteUser('{{$cliente->id}}','{{$cliente->name}}')"><i class="fa fa-trash"></i></a>														
													</div>
												</td>
											</tr>
										@endif
										@include('User.modals.delete_user_list',['user' => $cliente])
									@endforeach
								@endif
							</tbody>
						</table>
      				</div>
				</div>
				<div class="clearfix"> </div>
			</div>
			<div class="content-bottom">
          		{{--@include('Dashboard.modals.modal_intervalos');--}}
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
	<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
	<script>
		var table = $('#tabla2').DataTable({
			"scrollX": true
		});

		function deleteUser(user_id,user_name)
        {        	
        	$('#user_id').val(user_id);
        	$('#user_name').text(user_name);
        }
        $("#btn-eliminar-user").click(function(){
			var user_id = $("input[name=user_id]").val();
			console.log(user_id);
		 	$.ajax({
		 		type: "POST",
	            url: "{{route('eliminar.user.list')}}",
	            data : {'user_id':user_id, '_token': $('.token-container input[name=_token]').val()},
	            success: function (data) {
	            	location.reload();
		            $('#delete_client_modal').modal('hide');
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
	 	});
	</script>
	<script>
        $(function () {
            $('#supported').text('Supported/allowed: ' + !!screenfull.enabled);

            if (!screenfull.enabled) {
                return false;
            }           

            $('#toggle').click(function () {
                screenfull.toggle($('#container')[0]);
            });
            

            
        });
    </script>    
@endsection