<div class="modal fade" tabindex="-1" role="dialog" id="modalEnergyMeter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Contadores</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<div class="row" id="energyMeterListContainer">
            		<div class="col-12 text-right mb-3">
            			<button class="btn btn-primary btn-sm" id="btnAddEnergy"><span class="fa fa-plus"></span> Añadir Contador</button>
            		</div>
            		<div class="col-12">
            			<table class="table table-striped table-responsive bg-white mt-3" id="dtEnergy">
                          <thead class="bg-submeter-4">
                            <tr>
                              <th class="text-white" scope="col">ID</th>
                              <th class="text-white" scope="col" width="80%">Label</th>
                              <th class="text-white" scope="col">Asignar</th>
                              <th class="text-white" scope="col">Editar</th>
                              <th class="text-white" scope="col">Eliminar</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
            		</div>
            	</div>
            	<div class="row" id="energyMeterFormContainer">
            		<div class="col-12">
            			<form id="energyForm" action="" method="post" enctype="multipart/form-data">
            				{!! csrf_field() !!}
            				<div id="form_data">
            				</div>
                			<div class="row">
                				<div class="col-12">
                    				<div class="form-group">
                        				<label for="name">Nombre</label>
                                        <input type="text" class="form-control" id="energyName" name="name">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                        			<div class="form-group">
                        				<label for="host">Host</label>
                                        <input type="text" class="form-control" id="energyHost" name="host">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                        			<div class="form-group">
                        				<label for="database">Database</label>
                                        <input type="text" class="form-control" id="energyDatabase" name="database">
                                        <div class="invalid-feedback"></div>
                    				</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                    				<div class="form-group">
                        				<label for="user">User</label>
                                        <input type="text" class="form-control" id="energyUser" name="username">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-12 col-lg-6">
                    				<div class="form-group">
                        				<label for="port">Password</label>
                                        <input type="text" class="form-control" id="energyPassword" name="password">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-6 col-md-4 col-lg-3">
                    				<div class="form-group">
                        				<label for="port">Port</label>
                                        <input type="number" class="form-control" id="energyPort" name="port">
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-6 col-md-4 col-lg-4">
                    				<div class="form-group">
                        				<label for="port">Tarifa</label>
                                        <select class="form-control" name="rate" id="energyRate">
                                        	<option value="1">6.0</option>
                        					<option value="2">3.0</option>
                        					<option value="3">3.1</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                        			</div>
                    			</div>
                    			<div class="col-8 col-md-4 col-lg-4">
                    				<div class="form-group">
                        				<label for="port">Tipo</label>
                                        <select class="form-control" name="type" id="energyType">
                                        	<option value="1">Consumo</option>
                        					<option value="2">Generación</option>
                        					<option value="3">Gas</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                        			</div>
								</div>
								<div class="col-12 mb-2">
									<h5>
										<i class="fa fa-database"></i>
										Bases de datos de producción
									</h5>
									<hr>
									<div id="connections"></div>
									<hr>
								</div>
								<br>
                    			
                    			<div class="col-12 col-lg-6 pb-3">
                    				<div class="card">
                    					<div class="card-header bg-submeter-4 text-white text-center">
                    						<h5>Tipo de contrato de Energía</h5>
                    					</div>
                    					<div class="card-body text-left pl-lg-5">
                    						<div class="form-control border-0">
                        						<div class="form-check">
                                                    <input class="form-check-input" type="radio" name="contract" id="energyContract1" value="0" checked>
                                                    <label class="form-check-label" for="energyType1">
        												Precio Fijo
      												</label>
    											</div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="contract" id="energyContract2" value="1">
                                                    <label class="form-check-label" for="energyType2">
                                                    	Indexado
                                                    </label>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                    					</div>
                    				</div>
                    			</div>
                    			<div class="col-12 col-lg-6 pb-3">
                    				<div class="card">
                    					<div class="card-header bg-submeter-4 text-white text-center">
                    						<h5>Perfil de usuario</h5>
                    					</div>
                    					<div class="card-body text-left pl-lg-5">
                    						<div class="form-control border-0">
                        						<div class="form-check">
                                                    <input class="form-check-input" type="radio" name="profile" id="energyProfile1" value="0" checked>
                                                    <label class="form-check-label" for="energyProfile1">
        												Gestion (admin)
      												</label>
    											</div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="profile" id="energyProfile2" value="1">
                                                    <label class="form-check-label" for="energyProfile2">
                                                    	Técnico
                                                    </label>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                    					</div>
                    				</div>
                    			</div>
                    			<div class="col-12 col-lg-6 offset-lg-3">
                    				<div class="card">
                    					<div class="card-header bg-submeter-4 text-white text-center">
                    						<h5>Logotipo corporativo</h5>
                    					</div>
                    					<div class="card-body text-center pt-1">
                    						<div class="row" id="cntimage" style="display:none;">
                    							<div class="col-4 offset-4 px-0 py-2">
                    								<img src="" alt="Logo" class="img-fluid img-thumbnail">
                    							</div>
                    						</div>
                    						<div class="row">
                    							<div class="col-12 p-0">
                            						<div class="form-group mb-1">
            										 	<input type="file" name="avatar" id="avatar" class="file-hidden" id="fileImage">
            										 	<label class="btn btn-primary" for="avatar"><span class="fa fa-upload"></span> Examinar</label>                                                                                                                                                        
                                                    </div>
                                                    <span style="color:#000;">(Tamaño 360x360 pixeles)</span>
                                                </div>
                                            </div>
                    					</div>
                    				</div>
                    			</div>
                			</div>
            			</form>
            		</div>
            	</div>
            	<div class="row" id="energyMeterDeleteContainer">
            		<div class="col-12 text-center">
            			<form id="deleteEnergyForm" action="" method="post" enctype="multipart/form-data">
            				{!! csrf_field() !!}
            				<input type="hidden" name="_method" value="DELETE" />
							<input type="hidden" name="id" value="" />
            			</form>
            			<p>¿Está seguro que desea eliminar el contador <strong class="energy-name"></strong>?</p>
        			</div>        		
            	</div>
            </div>
            <div class="modal-footer">
            	<div id="btnEnergyList">
                	<button type="button" class="btn btn-secondary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                </div>
                <div id="btnEnergySave">
                	<button type="button" class="btn btn-danger btn-energy-list"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-primary" id="btnSaveEnergy"><span class="fa fa-save"></span> Guardar</button>
                	<button type="button" class="btn btn-success" id="btnSaveAssignEnergy"><span class="fa fa-check"></span> Guardar y Asignar</button>
                </div>
                <div id="btnEnergyDelete">
                	<button type="button" class="btn btn-primary btn-energy-list"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-danger" id="btnConfirmDeleteEnergy"><span class="fa fa-trash"></span> Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-delete-conection">
	<div class="modal-dialog modal-sm" role="document">
	  <div class="modal-content shadow">
		<div class="modal-header">
		  <h5 class="modal-title">
			  <i class="fa fa-trash"></i>
			  Eliminar conección
		  </h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		  <p>¿Esta seguro que desea eliminar la conección?</p>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-primary" data-id="0">
			  <i class="fa fa-check"></i>
			  Eliminar
		  </button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">
			  <i class="fa fa-times"></i>
			  Cancelar
		  </button>
		</div>
	  </div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal-form-conection">
	<div class="modal-dialog " role="document">
	  <div class="modal-content shadow">
		<div class="modal-header">
		  <h5 class="modal-title">
			  
		  </h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
		    <form  action="" method="post" enctype="multipart/form-data">
				{!! csrf_field() !!}
				<div class="row">
					<div class="form-group col-6">
						<label for="name">Nombre</label>
						<input type="text" class="form-control" name="name">
						<div class="invalid-feedback"></div>
					</div>
					<div class="form-group col-6">
						<label for="host">Host</label>
						<input type="text" class="form-control" name="host">
						<div class="invalid-feedback"></div>
					</div>
					<div class="form-group col-8">
						<label for="database">Database</label>
						<input type="text" class="form-control"  name="database">
						<div class="invalid-feedback"></div>
					</div>
					<div class="form-group col-4">
						<label for="port">Port</label>
						<input type="number" class="form-control" name="port">
						<div class="invalid-feedback"></div>
					</div>
					<div class="form-group col-6">
						<label for="user">User</label>
						<input type="text" class="form-control"  name="username">
						<div class="invalid-feedback"></div>
					</div>
					<div class="form-group col-6">
						<label for="port">Password</label>
						<input type="text" class="form-control" name="password">
						<div class="invalid-feedback"></div>
					</div>
					<div class="col-6 col-md-4 col-lg-3">
						
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-primary" data-id="">
			  <i class="fa fa-check"></i>
			  Guardar
		  </button>
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">
			  <i class="fa fa-times"></i>
			  Cancelar
		  </button>
		</div>
	  </div>
	</div>
</div>

<template id="energyEditFields">
	<input type="hidden" name="_method" value="PATCH" />
	<input type="hidden" name="id" value="" />
</template>
<template id="energyAssignButton">
	<button class="btn btn-success btn-sm btn-assign-energy" data-id="###"><span class="fa fa-check"></span></button>
</template>
<template id="energyEditButton">
	<button class="btn btn-primary btn-sm btn-edit-energy" data-id="###"><span class="fa fa-pen-fancy"></span></button>
</template>
<template id="energyDeleteButton">
	<button class="btn btn-danger btn-sm btn-delete-energy" data-id="###"><span class="fa fa-times"></span></button>
</template>
<div class="spinner">
	<div class="double-bounce1"></div>
	<div class="double-bounce2"></div>
  </div>
{{-- @Leo W Modificaciones para  garantizar que se especifican las BD de producción accesibles por cada contador --}}
@section('scripts') 
@parent

    <style>
		.spinner {
  width: 40px;
  height: 40px;

  position: relative;
  margin: 100px auto;
}

.double-bounce1, .double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #333;
  opacity: 0.6;
  position: absolute;
  top: 0;
  left: 0;
  
  -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.double-bounce2 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

@-webkit-keyframes sk-bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bounce {
  0%, 100% { 
    transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% { 
    transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}
	</style>
	
	
<script type="text/javascript">
	var dbProductions = {
		list: [],
		single: {},
		data: [
			
		],
		init: function (params) {
			var _this = this;
			dbProductions.render();
			$(document).on('click','#btn-add-conextion',function(){_this.onShowInsert()});
			$(document).on('click','[action-delete]',function(){_this.onShowDelete($(this).attr('action-delete'))});
			$(document).on('click','[action-update]',function(){_this.onShowUpdate($(this).attr('action-update'))});
			$(document).on('click','#modal-delete-conection button[data-id]',function(){_this.delete($(this).attr('data-id'))});
			$(document).on('click','#modal-form-conection button.btn-primary',function(){_this.save($(this).attr('data-id'))});

			

			$('body').on('hidden.bs.modal', function () {
				if($('.modal.show').length > 0)
				{
					$('body').addClass('modal-open');
				}
			});
		},
		setData(data){
			if(!data) data = [];
			this.data = data;
			this.render();
		},
		onShowInsert: function(){
			$('#modal-form-conection').modal({
				backdrop: false
			});
			$('#modal-form-conection .modal-title').html('<i class="fa fa-plus-circle"></i> Agregar conección');
			$('#modal-form-conection [name="name"]').val('');
			$('#modal-form-conection [name="host"]').val('');
			$('#modal-form-conection [name="database"]').val('');
			$('#modal-form-conection [name="port"]').val('');
			$('#modal-form-conection [name="username"]').val('');
			$('#modal-form-conection [name="password"]').val('');

			$('#modal-form-conection button[data-id]').attr('data-id',0);
		},
		onShowUpdate:	function(id){
			$('#modal-form-conection').modal({});
			$('#modal-form-conection .modal-title').html('<i class="fa fa-edit"></i> Modificar conección');
			
			$('#modal-form-conection [name="name"]').val($('tr[data-id="'+id+'"] [data-field="name"]').html());
			$('#modal-form-conection [name="host"]').val($('tr[data-id="'+id+'"] [data-field="host"]').html());
			$('#modal-form-conection [name="database"]').val($('tr[data-id="'+id+'"] [data-field="database"]').html());
			$('#modal-form-conection [name="port"]').val($('tr[data-id="'+id+'"] [data-field="port"]').html());
			$('#modal-form-conection [name="username"]').val($('tr[data-id="'+id+'"] [data-field="username"]').html());
			$('#modal-form-conection [name="password"]').val($('tr[data-id="'+id+'"] [data-field="password"]').html());

			$('#modal-form-conection button[data-id]').attr('data-id',id);
		},
		onShowDelete:function(id){
			$('#modal-delete-conection').modal({});
			$('#modal-delete-conection button[data-id]').attr('data-id',id);
		},
		save: function(id){
			
			var data = {
				id: id,
				name: $('#modal-form-conection [name="name"]').val(),
				database: $('#modal-form-conection [name="database"]').val(),
				username: $('#modal-form-conection [name="username"]').val(),
				host:$('#modal-form-conection [name="host"]').val(),
				port: $('#modal-form-conection [name="port"]').val(),
				password: $('#modal-form-conection [name="password"]').val()
			};
			if(id == 0)
			{
				$('table tr[data-id]').each(function(){
					var row = $(this);
					if(row.attr('data-id') > id ) id = row.attr('data-id');
				});
				data.id = parseInt(id) + 1;
				this.data.push(data);
			}else{
				const dtCopy = [];
				for (let index = 0; index < this.data.length; index++) {
					const element = this.data[index];
					if(element.id == id) 
					{
						this.data[index] = data;
					}
				}
				this.render();
			}
			
			this.render();
			$('#modal-form-conection').modal('hide');
		},
		delete:function(id){
			
			$('#modal-delete-conection').modal('hide');
			const dtCopy = [];
			for (let index = 0; index < this.data.length; index++) {
				const element = this.data[index];
				if(element.id != id) dtCopy.push(element);
			}
			this.data = dtCopy;
			this.render();
		},
		toFormData:function(frm){
			//formData.append('',dbProductions.formData())
			var index = 0;
			$('table tr[data-id]').each(function(){
				var row = $(this);
				frm.append('production_databases['+index+'][id]',row.attr('data-id'));
				frm.append('production_databases['+index+'][name]',row.find('td[data-field="name"]').html());
				frm.append('production_databases['+index+'][host]',row.find('td[data-field="host"]').html());
				frm.append('production_databases['+index+'][database]',row.find('td[data-field="database"]').html());
				frm.append('production_databases['+index+'][username]',row.find('td[data-field="username"]').html());
				frm.append('production_databases['+index+'][password]',row.find('td[data-field="password"]').html());
				frm.append('production_databases['+index+'][port]',row.find('td[data-field="port"]').html());
				index ++;
			});
			return frm;
		},
		render: function(){
			var rows = '';
			for (let index = 0; index < this.data.length; index++) {
				const element = this.data[index];
				rows += '<tr data-id="'+element.id+'"><td data-field="name">'+element.name+'</td><td data-field="host">'+element.host+'</td><td data-field="username">'+element.username+'</td><td data-field="database">'+element.database+'</td> <td data-field="port" class="hidden">'+element.port+'</td><td data-field="password" class="hidden">'+element.password+'</td>' + 
				  	    '<td><button type="button" class="btn btn-sm btn-outline-primary" action-update="'+element.id+'" style="width: 35px;"><i class="fa fa-edit"></i></button><button type="button" class="btn btn-sm btn-outline-danger" style="width: 35px;" action-delete="'+element.id+'"><i class="fa fa-trash"></i></button></td></tr>';
			}
			var tpl = `
				<div class="text-right">
					<button id="btn-add-conextion" type="button" class="btn btn-primary">Nueva conexión</button>	
				</div>
				<div class="box">
					<table class="table table-sm">
						<thead>
							<tr>
								<th>Alias/nombre</th>	
								<th>Servidor</th>	
								<th>Usuario</th>	
								<th>Base de datos</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							`+rows+`
						</tbody>
					</table>
				</div>
			`;
			$('#connections').html(tpl);
		}
	};

	
	$(document).ready(function() {
		dbProductions.init();
	});
	
	<!--
	var tokenEnergy = "{{csrf_token()}}";
	var urlListEnergy = "{{ route('energymeter.index') }}";
	var urlShowEnergy = "{{ route('energymeter.show', ['meter_id' => '?']) }}";
	var urlSaveEnergy = "{{ route('energymeter.save') }}";
	var urlUpdateEnergy = "{{ route('energymeter.update', ['meter_id' => '?']) }}";
	var urlDeleteEnergy = "{{ route('energymeter.delete', ['meter_id' => '?']) }}";
	
	var initializeEnergy = function() {
		$("#btnEnergyList").show();
		$("#btnEnergySave").hide();
		$("#btnEnergyDelete").hide();
		$("#energyMeterListContainer").show();
		$("#energyMeterDeleteContainer").hide();
		$("#energyMeterFormContainer").hide();
		$("#modalEnergyMeter").modal('show');	
	}
	
	var resetEnergy = function() {
		$("#btnEnergyList").show();
		$("#btnEnergySave").hide();
		$("#btnEnergyDelete").hide();
		$("#energyMeterListContainer").show();
		$("#energyMeterDeleteContainer").hide();
		$("#energyMeterFormContainer").hide();
		$("#modalEnergyMeter").modal('show');	
		$("#dtEnergy").DataTable().draw();
	}
	
	var newEnergy = function() {
		$("#energyForm #cntimage").hide();
		$("#energyForm")[0].reset();
		$("#modalEnergyMeter").data("action", urlSaveEnergy);
		$("#energyForm #form_data").html("");
		$("#energyMeterListContainer").hide();
		$("#energyMeterDeleteContainer").hide();
		$("#energyMeterFormContainer").show();
		$("#btnEnergySave").show();
		$("#btnEnergyList").hide();	
		$("#btnEnergyDelete").hide();
	}
	
	var editEnergyHandler = function(event) {
		event.preventDefault();
		var id = $(this).data("id");
		editEnergy(id);
	}
	
	var editEnergy = function(id) {
		
		var action = urlShowEnergy.replace("?", id);
		$.ajax({
			 type: "GET",
			 url: action,
			dataType: "json",
			success: function (data) {
				if(data.error) {
					return false;
				}
				$("#energyForm")[0].reset();
				$("#energyForm #form_data").html($("#energyEditFields").html());
				for(var key in data.data){
					var valData = data.data[key];
					$("#energyForm [type='hidden'][name='" + key + "']").val(valData);
					$("#energyForm [type='text'][name='" + key + "']").val(valData);
					$("#energyForm [type='number'][name='" + key + "']").val(valData);
					$("#energyForm select[name='" + key + "']").val(valData);
					$("#energyForm [type='radio'][name='" + key + "'][value='" + valData + "']").prop("checked", "checked");				
				}
				$("#energyForm #cntimage").hide();
				if(data.data["image"].length > 0) {
					$("#energyForm #cntimage").show();
					$("#energyForm img").attr("src", data.data["image"]);
				}
				
				$("#modalEnergyMeter").data("action", urlUpdateEnergy.replace('?', id));        	
				$("#energyMeterListContainer").hide();
				$("#energyMeterDeleteContainer").hide();
				$("#energyMeterFormContainer").show();
				$("#btnEnergySave").show();
				$("#btnEnergyList").hide();	
				$("#btnEnergyDelete").hide();

				dbProductions.setData(data.data.production_databases);
				
				/*if(data.data.production_databases)
				{
					for (let index = 0; index < data.data.production_databases.length; index++) {
						const db = data.data.production_databases[index];
						var option = new Option(db, db);
						option.selected = true;
						$("select[name='production_databases[]']").append(option);
						$("select[name='production_databases[]']").trigger("change");		
					}
				}*/
				
				
			},
			error: function (data) {
				console.log(data);
			}	            
		});
		
	}
	
	var sendFormEnergy = function(returnFunction, action){
		var form = $("#energyForm")[0];
		var formData = new FormData(form);
		formData = dbProductions.toFormData(formData);
		$("#energyForm input, #energyForm select").removeClass("border-error");
		$("#energyForm .invalid-feedback").html("");
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
						var input = $("#energyForm [name='" + key + "']");
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
	
	var returnSaveEnergy = function(data){
		resetEnergy();
	};
	
	var returnSaveAssignEnergy = function(data){
		var assignFunction = $("#modalEnergyMeter").data("assign");
		if(assignFunction != undefined) {
			var row = [data.id, data.count_label, 1, 1];
			assignFunction(row);
			$("#modalEnergyMeter").modal("hide");
		}
	};
	
	var assignEnergy = function(event) {
		event.preventDefault();
		var assignFunction = $("#modalEnergyMeter").data("assign");
		if(assignFunction != undefined) {
			var rowObject = $(this).closest("tr");
			if(rowObject.length > 0) {
				var data = $("#dtEnergy").DataTable().row(rowObject[0]).data();
				var row = [data[0], data[1], 1, 1];
				assignFunction(row);
				$("#modalEnergyMeter").modal("hide");
			}
		}
	}
	
	var deleteEnergy = function(event) {
		event.preventDefault();
		
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			var data = $("#dtEnergy").DataTable().row(rowObject[0]).data();
			$("#deleteEnergyForm [name='id']").val(data[0]);
			$("#energyMeterDeleteContainer .energy-name").html(data[1]);
			$("#energyMeterDeleteContainer").show();
			$("#energyMeterListContainer").hide();
			$("#energyMeterFormContainer").hide();
			$("#btnEnergySave").hide();
			$("#btnEnergyList").hide();	
			$("#btnEnergyDelete").show();
		}
	}
	
	var confirmDeleteEnergy = function(event) {
		event.preventDefault();
		var id = $("#deleteEnergyForm [name='id']").val();
		var form = $("#deleteEnergyForm")[0];
		var formData = new FormData(form);
		var action = urlDeleteEnergy.replace("?", id);
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
				resetEnergy();
			},
			error: function (data) {
				console.log(data);
			}	            
		});
	}
	
	var initListEnergy = function() {
		$("#dtEnergy").DataTable({
			"processing": true,
			"serverSide": true,
			"ajax": urlListEnergy,
			"autoWidth": false,
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
					"targets": [ 2, 3, 4 ],
					"visible": true,
					"searchable": false,
					"sortable": false
				},
				{
					"targets": [ 2 ],                
					"render": function ( data, type, row ) {
						var assignedData = $("#modalEnergyMeter").data("assigned");
						if(assignedData != undefined) {
							if(Array.isArray(assignedData)){
								if(assignedData.indexOf(row[0]) >= 0){
									return '';
								}
							}                    	
						}
						var html = $("#energyAssignButton").html();
						html = html.replace("###", row[0]);
						return html;
					}
				},
				{
					"targets": [ 3 ],                
					"render": function ( data, type, row ) {
						var html = $("#energyEditButton").html();
						html = html.replace("###", row[0]);
						return html;
					}
				},
				{
					"targets": [ 4 ],                
					"render": function ( data, type, row ) {
						var html = $("#energyDeleteButton").html();
						html = html.replace("###", row[0]);
						return html;
					}
				}
			],
			"drawCallback": function( settings ) {
				$(".btn-edit-energy").off("click").click(editEnergyHandler);
				$(".btn-assign-energy").off("click").click(assignEnergy);
				$(".btn-delete-energy").off("click").click(deleteEnergy);
			}
		});
	}
	
	$(document).ready(function(){
		$("#modalEnergyMeter").data("initialize", initializeEnergy);
		$("#modalEnergyMeter").data("reset", resetEnergy);
		$("#modalEnergyMeter").data("new", newEnergy);
		$("#modalEnergyMeter").data("edit", editEnergy);
		$(".btn-energy-list").click(initializeEnergy);	
		
		$("#btnAddEnergy").click(function(){
			var newForm = $("#modalEnergyMeter").data("new");
			if(newForm != undefined) {
				newForm();
				dbProductions.setData([]);
			}
		});
	
		$(".edit-energy").click(function(){
			var editForm = $("#modalEnergyMeter").data("edit");
			if(editForm != undefined) {
				editForm();
			}
		});
	
		$("#btnSaveEnergy").click(function(event) {
			event.preventDefault();
			var action = $("#modalEnergyMeter").data("action");
			sendFormEnergy(returnSaveEnergy, action);
		});
	
		$("#btnSaveAssignEnergy").click(function(event) {
			event.preventDefault();
			var action = $("#modalEnergyMeter").data("action");
			sendFormEnergy(returnSaveAssignEnergy, action);
		});
	
		$("#modalEnergyMeter").on("shown.bs.modal", function(){
			$("#dtEnergy").removeAttr("style");
			$("#dtEnergy").DataTable().columns.adjust();
		});
	
		$("#btnConfirmDeleteEnergy").click(confirmDeleteEnergy);
	
		initListEnergy();
	});
	
	//-->
	</script>
@endsection

