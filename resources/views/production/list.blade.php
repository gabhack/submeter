@extends('Dashboard.layouts.global4')

@section('content')
    <div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
    	<div class="banner col-md-12 mb-4 mr-3">
    		<h3>Listado de Configuraciones de Producción</h3>
    		<hr/>
    		@if (session('message.production'))
    			<div class="row">
    				<div class="col-12">
                    	<div class="alert alert-success" role="alert">
                          {{session('message.production')}}
                        </div>
                    </div>
                </div>
            @endif
            <!--<div class="row mb-md-4">
				<div class="col-12 text-md-right">
					<a href="{{ route('production.create') }}" class="btn btn-success text-white"><span class="fa fa-plus"></span> Nueva Configuración</a>
				</div>
    		</div>-->

            <div class="row mb-md-4">
                <div class="col-12">
                    <a href="{{ route('production.create') }}" class="btn btn-success text-white float-left"><span class="fa fa-plus "></span> Nueva Configuración</a>
                   
                    {{-- <a  class="btn btn-primary text-white float-right"  data-toggle="modal" data-target="#newModalConnection"><span class="fa fa-plus"></span> Nueva Conexión</a>
                    Leo W quitamos esta opción --}}
                    <a href="{{ route('production.data',[$user->id]) }}" class="btn btn-primary text-white float-right"  ><span class="fa fa-undo"></span> Regresar</a>
                </div>
            </div>

    		<div class="row">
    			<div class="col-12">
    				<table class="table table-striped bg-white mt-3" id="dtList">
                      <thead class="bg-submeter-4">
                        <tr>
                          <th class="text-white" scope="col">ID</th>
                          <th class="text-white" scope="col" width="100%">Nombre</th>
                          <th class="text-white" scope="col">Editar</th>
                          <th class="text-white" scope="col">Eliminar</th>                          
                        </tr>
                      </thead>
                    	<tbody>
							@foreach($productions as $production)
								<tr>
									<td>{{$production->id}}</td>
									<td>{{$production->name}}</td>
									<td class="text-white" scope="col"><a class="btn btn-primary" href="{{ route('production.edit', ['id' => $production->id]) }}"><span class="fa fa-pen"></span></a></td>                      
                          			<td class="text-white" scope="col"><button class="btn btn-danger delete-register" data-id="{{$production->id}}"><span class="fa fa-times"></span></button></td>
								</tr>
							@endforeach
                      	</tbody>
                    </table>
    			</div>
    		</div>
    	</div>
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="modalConfirm">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="text-center">¿Está seguro que desea eliminar el registro?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><span class="fa fa-times"></span> Cancelar</button>
            <button type="button" class="btn btn-danger" id="btnConfirm"><span class="fa fa-trash"></span> Eliminar</button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="modalDeleteMessage">
    	<div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                	<h5 class="modal-title">Mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                	<p class="text-center">Se ha eliminado correctamente el registro.</p>
                </div>
                <div class="modal-footer">
                	<button type="button" class="btn btn-primary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                </div>
            </div>
    	</div>
    </div>
@include("production.modal.newconnections")   
@endsection

@section('scripts')
<script type="text/javascript">
<!--

    var token = "{!! csrf_token() !!}";
    var urlDelete = "{{route('production.delete', ['id' => '_XXX_'])}}";
    
    function confirmDelete() {
    	var id = $(this).data("id");
    	$("#modalConfirm").modal("show");
    	$("#btnConfirm").data("id", id);
    }
    
    function deleteRegistry() {
    	var id = $(this).data("id");
    	var url = urlDelete.replace("_XXX_", id);
    	
    	var data = {
    		_method: "DELETE",
    		_token: token
    	};
    	
    	$.ajax({
      	 	 url: url,
    	     cache: false,
    	     data: data,
    	     dataType: "json",
    	     type: 'POST',
    	     success: function(data){
    	    	 $("#modalConfirm").modal("hide");
    	    	 $('#modalDeleteMessage').modal("show");
    	     },
    	     error : function(error){
    			if(error.status == 422) {
    				
    			}
    	     }
    	});
    }

    
    var initListDt = function() {
    	$("#dtList").dataTable({
    		"language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "autoWidth": false,
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 2, 3],
                    "visible": true,
                    "searchable": false,
                    "sortable": false
                }
            ],
            "drawCallback": function( settings ) {
                $(".delete-register").off("click").click(confirmDelete);
            }
    	});
    }

	$(document).ready(function(){
		initListDt();

		$("#btnConfirm").click(deleteRegistry);
		$('#modalDeleteMessage').on('hidden.bs.modal', function(){
			location.reload();
		});
	});
//-->
</script>
@endsection