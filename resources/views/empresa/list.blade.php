@extends('Dashboard.layouts.global4')

@section('content')
    <div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
    	<div class="banner col-md-12 mb-4 mr-3">
    		<h3>Lista de Empresa</h3>
    		<hr/>
    		@if (session('message.enterprise'))
    			<div class="row">
    				<div class="col-12">
                    	<div class="alert alert-success" role="alert">
                          {{session('message.enterprise')}}
                        </div>
                    </div>
                </div>
            @endif
            <div class="row mb-md-4">
				<div class="col-12 text-md-right">
					<a href="{{ route('enterprise.create') }}" class="btn btn-success text-white"><span class="fa fa-plus"></span> Nueva Empresa</a>
				</div>
    		</div>
    		<div class="row">
    			<div class="col-12">
    				<table class="table table-striped bg-white mt-3" id="dtList">
                      <thead class="bg-submeter-4">
                        <tr>
                          <th class="text-white" scope="col">ID</th>
                          <th class="text-white" scope="col" width="100%">Nombre</th>
                          <th class="text-white" scope="col">Ver</th>
                          <th class="text-white" scope="col">Editar</th>
                          <th class="text-white" scope="col">Eliminar</th>                          
                        </tr>
                      </thead>
                    	<tbody>
							@foreach($empresas as $empresa)
								<tr>
									<td>{{$empresa->id}}</td>
									<td>{{$empresa->name}}</td>
									<td class="text-white" scope="col"><button class="btn btn-secondary show-users" data-id="{{$empresa->id}}"><span class="fa fa-eye"></span></button></td>
									<td class="text-white" scope="col"><a class="btn btn-primary" href="{{ route('enterprise.edit', ['enterprise_id' => $empresa->id]) }}"><span class="fa fa-pen"></span></a></td>                      
                          			<td class="text-white" scope="col"><button class="btn btn-danger delete-register" data-id="{{$empresa->id}}"><span class="fa fa-times"></span></button></td>
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
    
    <div class="modal fade" tabindex="-1" role="dialog" id="modalShowEnterprise">
    	<div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                	<h5 class="modal-title">Usuarios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    	<span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                	<div class="row">
            			<div class="col-12">
            				<table class="table table-striped bg-white mt-3" id="dtUsers">
                              <thead class="bg-submeter-4">
                                <tr>
                                  <th class="text-white" scope="col">ID</th>
                                  <th class="text-white" scope="col" width="50%">Nombre</th>
                                  <th class="text-white" scope="col" width="50%">E-mail</th>
                                  <th class="text-white" scope="col">Ver</th>                          
                                </tr>
                              </thead>
                              <tbody></tbody>
                            </table>
            			</div>
            		</div>
                </div>
                <div class="modal-footer">
                	<button type="button" class="btn btn-primary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                </div>
            </div>
    	</div>
    </div>
    
    <template id="tplBtnShow">
    	<a href="#" class="btn btn-secondary"><span class="fa fa-eye"></span></a>
    </template>
@endsection

@section('scripts')
<script type="text/javascript">
<!--

    var token = "{!! csrf_token() !!}";
    var urlShow = "{{ route('resumen.energia.potencia', ['user_id' => '_XXX_'] )}}"
    var urlUsers = "{{route('enterprise.users', ['enterprise_id' => '_XXX_'])}}";
    var urlDelete = "{{route('enterprise.delete', ['enterprise_id' => '_XXX_'])}}";
    
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

    function showUsers() {
    	var id = $(this).data("id");
    	var url = urlUsers.replace("_XXX_", id);

    	var table = $('#dtUsers').DataTable();
    	 
    	table.clear().draw();
    	
    	$.ajax({
      	 	 url: url,
    	     cache: false,    	     
    	     dataType: "json",
    	     type: 'GET',
    	     success: function(data){
        	     if(data.error) {
					return false;
        	     }
        	     var dataUsers = data.data;
        	     for(var i = 0; i < dataUsers.length; i++) {
        	    	 table.row.add( [
        	             dataUsers[i].id,
        	             dataUsers[i].name,
        	             dataUsers[i].email,
        	             1
        	         ] );
        	     }
        	     table.draw();
    	    	 $("#modalShowEnterprise").modal("show");
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
                    "targets": [ 2, 3, 4],
                    "visible": true,
                    "searchable": false,
                    "sortable": false
                }
            ],
            "drawCallback": function( settings ) {
                $(".delete-register").off("click").click(confirmDelete);
                $(".show-users").off("click").click(showUsers);
            }
    	});
    }

    var initListUsers = function() {
    	$("#dtUsers").dataTable({
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
                    "targets": [ 3 ],
                    "visible": true,
                    "searchable": false,
                    "sortable": false,
                    "render": function ( data, type, row, meta ) {
                        var url = urlShow.replace("_XXX_", row[0]);
                        var html = $("#tplBtnShow").html();
                        html = $(html);
                        html.attr("href", url);
                    	return html[0].outerHTML;
                  	 }
                }
            ],
            "drawCallback": function( settings ) {
                $(".delete-register").off("click").click(confirmDelete);
                $(".show-users").off("click").click(showUsers);
            }
    	});
    }

	$(document).ready(function(){
		initListDt();
		initListUsers();

		$("#btnConfirm").click(deleteRegistry);
		$('#modalDeleteMessage').on('hidden.bs.modal', function(){
			location.reload();
		});
	});
//-->
</script>
@endsection