<div class="modal fade" tabindex="-1" role="dialog" id="modalUser">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<div class="row" id="userListContainer">
            		<div class="col-12 text-right mb-3">
            			<button class="btn btn-primary btn-sm" id="btnAddUser"><span class="fa fa-plus"></span> Añadir Usuario</button>
            		</div>
            		<div class="col-12">
            			<table class="table table-striped table-responsive bg-white mt-3" id="dtUser">
                          <thead class="bg-submeter-4">
                            <tr>
                              <th class="text-white" scope="col">ID</th>
                              <th class="text-white" scope="col" width="35%">Nombre</th>
                              <th class="text-white" scope="col" width="35%">Email</th>
                              <th class="text-white" scope="col">Asignar</th>
                              <th class="text-white" scope="col">Editar</th>
                              <th class="text-white" scope="col">Eliminar</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            		</div>
            	</div>
            	<div class="row" id="userFormContainer">
            		<div class="col-12">
            			<form id="userForm" action="" method="post" enctype="multipart/form-data">
            				{!! csrf_field() !!}
            				<div id="form_data">
            				</div>
                			<div class="row">
                				<div class="col-12">
                    				<div class="form-group">
                        				<label for="name">Nombre</label>
                                        <input type="text" class="form-control" id="userName" name="name">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                        			<div class="form-group">
                        				<label for="host">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="userEmail" name="email">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                        			<div class="form-group">
                        				<label for="database">Contraseña</label>
                                        <input type="password" class="form-control" id="userPassword" name="password">
                                        <div class="invalid-feedback"></div>
                    				</div>
                    			</div>
                    			<div class="col-12 col-md-8 col-lg-6">
                    				<div class="form-group">
                        				<label for="port">Tipo</label>
                                        <select class="form-control" name="type" id="userType">
                                        	<option value="1">Administrador</option>
                        					<option value="2">Usuario</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                			</div>
            			</form>
            		</div>
            	</div>
            	<div class="row" id="userDeleteContainer">
            		<div class="col-12 text-center">
            			<form id="deleteUserForm" action="" method="post" enctype="multipart/form-data">
            				{!! csrf_field() !!}
            				<input type="hidden" name="_method" value="DELETE" />
							<input type="hidden" name="id" value="" />
            			</form>
            			<p>¿Está seguro que desea eliminar el contador <strong class="user-name"></strong>?</p>
        			</div>        		
            	</div>
            </div>
            <div class="modal-footer">
            	<div id="btnUserList">
                	<button type="button" class="btn btn-secondary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                </div>
                <div id="btnUserSave">
                	<button type="button" class="btn btn-danger btn-user-list"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-primary" id="btnSaveUser"><span class="fa fa-save"></span> Guardar</button>
                	<button type="button" class="btn btn-success" id="btnSaveAssignUser"><span class="fa fa-check"></span> Guardar y Asignar</button>
                </div>
                <div id="btnUserDelete">
                	<button type="button" class="btn btn-primary btn-user-list"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-danger" id="btnConfirmDeleteUser"><span class="fa fa-trash"></span> Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<template id="userEditFields">
	<input type="hidden" name="_method" value="PATCH" />
	<input type="hidden" name="id" value="" />
</template>
<template id="userAssignButton">
	<button class="btn btn-success btn-sm btn-assign-user" data-id="###"><span class="fa fa-check"></span></button>
</template>
<template id="userEditButton">
	<button class="btn btn-primary btn-sm btn-edit-user" data-id="###"><span class="fa fa-pen-fancy"></span></button>
</template>
<template id="userDeleteButton">
	<button class="btn btn-danger btn-sm btn-delete-user" data-id="###"><span class="fa fa-times"></span></button>
</template>

<script type="text/javascript">
<!--
var tokenUser = "{{csrf_token()}}";
var urlListUser = "{{ route('user.index') }}";
var urlShowUser = "{{ route('user.show', ['user_id' => '?']) }}";
var urlSaveUser = "{{ route('user.save') }}";
var urlUpdateUser = "{{ route('user.update', ['user_id' => '?']) }}";
var urlDeleteUser = "{{ route('user.delete', ['user_id' => '?']) }}";

var initializeUser = function() {
	$("#btnUserList").show();
	$("#btnUserSave").hide();
	$("#btnUserDelete").hide();
	$("#userListContainer").show();
	$("#userDeleteContainer").hide();
	$("#userFormContainer").hide();
	$("#modalUser").modal('show');	
}

var resetUser = function() {
	$("#btnUserList").show();
	$("#btnUserSave").hide();
	$("#btnUserDelete").hide();
	$("#userListContainer").show();
	$("#userDeleteContainer").hide();
	$("#userFormContainer").hide();
	$("#modalUser").modal('show');	
	$("#dtUser").DataTable().draw();
}

var newUser = function() {
	$("#userForm")[0].reset();
	$("#modalUser").data("action", urlSaveUser);
	$("#userForm #form_data").html("");
	$("#userListContainer").hide();
	$("#userDeleteContainer").hide();
	$("#userFormContainer").show();
	$("#btnUserSave").show();
	$("#btnUserList").hide();	
	$("#btnUserDelete").hide();
}

var editUser = function(event) {
	event.preventDefault();
	var id = $(this).data("id");
	var action = urlShowUser.replace("?", id);
	$.ajax({
 		type: "GET",
 		url: action,
	    dataType: "json",
        success: function (data) {
            if(data.error) {
				return false;
            }
            $("#userForm")[0].reset();
            $("#userForm #form_data").html($("#userEditFields").html());
            for(var key in data.data){
				var valData = data.data[key];
				$("#userForm [type='hidden'][name='" + key + "']").val(valData);
				$("#userForm [type='text'][name='" + key + "']").val(valData);
				$("#userForm [type='email'][name='" + key + "']").val(valData);
				$("#userForm [type='number'][name='" + key + "']").val(valData);
				$("#userForm select[name='" + key + "']").val(valData);
				$("#userForm [type='radio'][name='" + key + "'][value='" + valData + "']").prop("checked", "checked");				
            }
        	$("#modalUser").data("action", urlUpdateUser.replace('?', id));        	
        	$("#userListContainer").hide();
        	$("#userDeleteContainer").hide();
        	$("#userFormContainer").show();
        	$("#btnUserSave").show();
        	$("#btnUserList").hide();	
        	$("#btnUserDelete").hide();
        },
        error: function (data) {
            console.log(data);
        }	            
    });
	
}

var sendFormUser = function(returnFunction, action){
	var form = $("#userForm")[0];
	var formData = new FormData(form);
	$("#userForm input, #userForm select").removeClass("border-error");
	$("#userForm .invalid-feedback").html("");
	$.ajax({
 		type: "POST",
 		url: action,
 		data: formData,
 		cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
        success: function (data) {
        	if(data.error) {
            	console.log(data);
            	for(key in data.messages){
                	var input = $("#userForm [name='" + key + "']");
                	if(input.length > 0) {
                    	input.addClass("border-error");
                		var parent = input.closest(".form-group");
                		if(parent.length > 0) {
                			parent.find(".invalid-feedback").html(data.messages[key]).show();
                		}
                	}
            	}
				return false;
        	}
        	returnFunction(data.data);
        },
        error: function (data) {
            console.log(data);
        }	            
    });
}

var returnSaveUser = function(data){
	resetUser();
};

var returnSaveAssignUser = function(data){
	var assignFunction = $("#modalUser").data("assign");
	if(assignFunction != undefined) {
		var row = [data.id, data.name, data.email, "", 0, 1, 1, 1];
		assignFunction(row);
		$("#modalUser").modal("hide");
	}
};

var assignUser = function(event) {
	event.preventDefault();
	var assignFunction = $("#modalUser").data("assign");
	if(assignFunction != undefined) {
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
    		var data = $("#dtUser").DataTable().row(rowObject[0]).data();
    		var row = [data[0], data[1], data[2], 0, 0, 1 , 1 ,1];
    		assignFunction(row);
    		$("#modalUser").modal("hide");
		}
	}
}

var deleteUser = function(event) {
	event.preventDefault();
	
	var rowObject = $(this).closest("tr");
	if(rowObject.length > 0) {
		var data = $("#dtUser").DataTable().row(rowObject[0]).data();
		$("#deleteUserForm [name='id']").val(data[0]);
		$("#userDeleteContainer .user-name").html(data[1]);
		$("#userDeleteContainer").show();
		$("#userListContainer").hide();
		$("#userFormContainer").hide();
		$("#btnUserSave").hide();
		$("#btnUserList").hide();	
		$("#btnUserDelete").show();
	}
}

var confirmDeleteUser = function(event) {
	event.preventDefault();
	var id = $("#deleteUserForm [name='id']").val();
	var form = $("#deleteUserForm")[0];
	var formData = new FormData(form);
	var action = urlDeleteUser.replace("?", id);
	$.ajax({
 		type: "POST",
 		url: action,
 		data: formData,
 		cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
        success: function (data) {
        	if(data.error) {
            	return false;
        	}
        	resetUser();
        },
        error: function (data) {
            console.log(data);
        }	            
    });
}

var initListUser = function() {
	$("#dtUser").DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": urlListUser,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        },
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 3, 4, 5 ],
                "visible": true,
                "searchable": false,
                "sortable": false
            },
            {
                "targets": [ 3 ],                
                "render": function ( data, type, row ) {
                    var assignedData = $("#modalUser").data("assigned");
                    if(assignedData != undefined) {
                        if(Array.isArray(assignedData)){
							if(assignedData.indexOf(row[0]) >= 0){
								return '';
							}
                        }                    	
                    }
                    var html = $("#userAssignButton").html();
                    html = html.replace("###", row[0]);
                    return html;
                }
            },
            {
                "targets": [ 4 ],                
                "render": function ( data, type, row ) {
                    var html = $("#userEditButton").html();
                    html = html.replace("###", row[0]);
                    return html;
                }
            },
            {
                "targets": [ 5 ],                
                "render": function ( data, type, row ) {
                    var html = $("#userDeleteButton").html();
                    html = html.replace("###", row[0]);
                    return html;
                }
            }
        ],
        "drawCallback": function( settings ) {
            $(".btn-edit-user").off("click").click(editUser);
            $(".btn-assign-user").off("click").click(assignUser);
            $(".btn-delete-user").off("click").click(deleteUser);
        }
	});
}

$(document).ready(function(){
	$("#modalUser").data("initialize", initializeUser);
	$("#modalUser").data("reset", resetUser);
	$("#modalUser").data("new", newUser);
	$("#modalUser").data("edit", editUser);
	$(".btn-user-list").click(initializeUser);	
	
	$("#btnAddUser").click(function(){
		var newForm = $("#modalUser").data("new");
		if(newForm != undefined) {
			newForm();
		}
	});

	$(".edit-user").click(function(){
		var editForm = $("#modalUser").data("edit");
		if(editForm != undefined) {
			editForm();
		}
	});

	$("#btnSaveUser").click(function(event) {
		event.preventDefault();
		var action = $("#modalUser").data("action");
		sendFormUser(returnSaveUser, action);
	});

	$("#btnSaveAssignUser").click(function(event) {
		event.preventDefault();
		var action = $("#modalUser").data("action");
		sendFormUser(returnSaveAssignUser, action);
	});

	$("#modalUser").on("shown.bs.modal", function(){
		$("#dtUser").removeAttr("style");
		$("#dtUser").DataTable().columns.adjust();
	});

	$("#btnConfirmDeleteUser").click(confirmDeleteUser);

	initListUser();
});

//-->
</script>