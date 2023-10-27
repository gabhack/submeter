@extends('Dashboard.layouts.global')

@section('content')

	<div id="wrapper">        
    	<div id="page-wrapper" class="gray-bg dashbard-1">
       		<div class="content-main">	
				<div class="content-mid" >					
					<div class="grid_3 col-md-12" style="height: 500%;">
						<div class="row" style="margin-bottom:15px;">
    						<div class="col-md-10">
    							<h3 class="head-top">Editar usuario</h3>
    						</div>
    						<div class="col-md-2 text-right">
    							<button class="btn btn-danger btn-return"><span class="fa fa-times" style="font-size:1.8em;"></span><br/> Cancelar</button>
    						</div>
						</div>
						@if (Session::has('message'))
			                <div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			            @endif

			            @if (Session::has('message-error'))
			                <div id="message-success" class="alert alert-danger">{{ Session::get('message-error') }}</div>
			            @endif

						<form class="form-horizontal" name="registrar_user" enctype='multipart/form-data' method="POST" autocomplete="off" novalidate action="{{ route('update.user', $client->id) }}">
	                        {{ csrf_field() }}

	                        <div id="name_div" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	                            <label for="name" class="col-md-1 control-label">Empresa</label>

	                            <div class="col-md-11">
	                                <input id="name" type="text" class="form-control" name="name" value="{{ $client->name }}" required autofocus>

	                                @if ($errors->has('name'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('name') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>

	                        <div id="email_div" class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
	                            <label for="email" class="col-md-1 control-label">Correo</label>

	                            <div class="col-md-11">
	                                <input id="email" type="email" class="form-control" name="email" value="{{ $client->email }}" required>

	                                @if ($errors->has('email'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('email') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>

	                        <div id="contadores_div" class="form-group{{ $errors->has('contadores') ? ' has-error' : '' }}">
	                        	@for($i = 0; $i < $contadores; $i++)
	                        		<div class = "col-md-12 text-center" style="margin-bottom: 60px;">
	                        			<h3 style="color:#004165;">Datos de Contador {{($i+1)}}</h3>
	                        			<br><br>
	                        			<div class="col-md-12">
		                        			<label class="col-md-1 control-label">Nombre: </label>
		                        			<div class="col-md-3">
		                        				<input id = "name_cont_{{$i}}" name = "name_cont_{{$i}}" class="form-control" value="{{$client->_count[$i]->count_label}}">
		                        			</div>
		                        			<label class="col-md-1 control-label">Host: </label>
		                        			<div class="col-md-3">
		                        				<input id = "val_host_{{$i}}" name = "val_host_{{$i}}" class="form-control" value="{{$client->_count[$i]->host}}">
		                        			</div>
		                        			<label class="col-md-1 control-label">DataBase: </label>
		                        			<div class="col-md-3">
		                        				<input id = "val_dbase_{{$i}}" name = "val_dbase_{{$i}}" class="form-control" value="{{$client->_count[$i]->database}}">
		                        			</div>
		                        		</div>
		                        		<div class = "col-md-12">
		                        			<br><br>
		                        			<label class="col-md-1 control-label">Port: </label>
		                        			<div class="col-md-2">
		                        				<input id = "val_port_{{$i}}" name = "val_port_{{$i}}" class="form-control" value="{{$client->_count[$i]->port}}">
		                        			</div>
		                        			<label class="col-md-1 control-label">Username: </label>
		                        			<div class="col-md-3">
		                        				<input id = "val_username_{{$i}}" name = "val_username_{{$i}}" class="form-control" value="{{$client->_count[$i]->username}}">
		                        			</div>
		                        			<label class="col-md-1 control-label">Password: </label>
		                        			<div class="col-md-3">
		                        				<input id = "val_password_{{$i}}" name = "val_password_{{$i}}" class="form-control" value="{{$client->_count[$i]->password}}">
		                        			</div>
		                        			<br><br>
		                        		</div>
		                        		<div class="col-md-12">
		                        			<div class="row" style="margin-top:10px;">
		                        				<div class="col-md-4">
		                        					<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
														<thead>
															<tr>
																<th>
																	Tipo de contrato de Energía
																</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="text-center">
																	<div class="radio-inline" style="margin-right:20px;">
                                                                      	<label>
                                                                      		@if($client->_count[$i]->tipo_contrato == 1)
                                                                          	<input type="radio" id="tipo_contrato_{{$i}}_1" name="tipo_contrato_{{$i}}" value="0" >
                                                                          	@else
                                                                          	<input type="radio" id="tipo_contrato_{{$i}}_1" name="tipo_contrato_{{$i}}" checked="checked" value="0">
                                                                          	@endif																			 
																			Precio Fijo
                                                                      	</label>
                                                                    </div>
                                                                    <div class="radio-inline">
                                                                      	<label>
                                                                      		@if($client->_count[$i]->tipo_contrato == 1)
                                                                          	<input type="radio" id="tipo_contrato_{{$i}}_2" name="tipo_contrato_{{$i}}" checked="checked" value="1">
                                                                          	@else
                                                                          	<input type="radio" id="tipo_contrato_{{$i}}_2" name="tipo_contrato_{{$i}}" value="1" >
                                                                          	@endif 
                                                                      		Indexado
                                                                      	</label>
                                                                    </div>
																</td>
															</tr>
														</tbody>
													</table>
		                        				</div>
		                        				<div class="col-md-4">
		                        					<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
														<thead>
															<tr>
																<th>
																	Perfil de Usuario
																</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="text-center">
																	<div class="radio-inline" style="margin-right:20px;">
                                                                    	<label>
                                                                    		@if($client->_count[$i]->tipo_usuario == 1)
                                                                          	<input type="radio" name="tipo_usuario_{{$i}}" id="tipo_usuario_{{$i}}_1" value="0" >
                                                                          	@else
                                                                          	<input type="radio" name="tipo_usuario_{{$i}}" id="tipo_usuario_{{$i}}_1" checked="checked" value="0" >
                                                                          	@endif                                                                           	
                                                                            Gestion (admin)
                                                                      	</label>
                                                                    </div>
                                                                    <div class="radio-inline">
                                                                      	<label>
                                                                      		@if($client->_count[$i]->tipo_usuario == 1)
                                                                          	<input type="radio" name="tipo_usuario_{{$i}}" id="tipo_usuario_{{$i}}_2" checked="checked" value="1" >
                                                                          	@else
                                                                          	<input type="radio" name="tipo_usuario_{{$i}}" id="tipo_usuario_{{$i}}_2" value="1"  >
                                                                          	@endif
                                                                            Técnico
                                                                      	</label>
                                                                    </div>
																</td>
															</tr>
														</tbody>
													</table>
		                        				</div>
		                        				<div class="col-md-4">
		                        					<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
														<thead>
															<tr>
																<th>
																	Logotipo Imagen Corporativa
																</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="text-center">
																	<div class="row">
																		<div class="col-md-8">
																			 <div class="form-group">
																			 	<input type="file" name="avatar_{{$i}}" id="avatar_{{$i}}" class="file-hidden" id="fileImage">
																			 	<label class="btn btn-primary" for="avatar_{{$i}}"><span class="fa fa-upload"></span> Examinar</label>                                                                                                                                                        
                                                                              </div>
                                                                              <span style="color:#000;">(Tamaño 360x360 pixeles)</span>
																		</div>
																		<div class="col-md-3">
																			@if($client->_count[$i]->url_image != '')
																			<img src="{{$client->_count[$i]->url_image}}" alt="Logo" class="img-responsive img-rounded">
																			@endif
																		</div>
																	</div>
																</td>
															</tr>
														</tbody>
													</table>
		                        				</div>
		                        			</div>
		                        		</div>
		                        		<div class = "col-md-12" style="margin-top:15px;">
		                        			<div class="row">
		                        				<div class="col-md-3">
		                        					<label class="col-md-3 control-label">Tarifa: </label>
        		                        			<div class="col-md-9">
        		                        				<select id = "tarifa_{{$i}}" name = "tarifa_{{$i}}" class="form-control">
        		                        					@if($client->_count[$i]->tarifa == 1)
            		                        					<option value="1" selected="selected">6.0</option>
            		                        					<option value="2">3.0</option>
            		                        					<option value="3">3.1</option>
        		                        					@elseif($client->_count[$i]->tarifa == 2)
        		                        						<option value="1">6.0</option>
            		                        					<option value="2" selected="selected">3.0</option>
            		                        					<option value="3">3.1</option>
        		                        					@elseif($client->_count[$i]->tarifa == 3)
        		                        						<option value="1">6.0</option>
            		                        					<option value="2">3.0</option>
            		                        					<option value="3" selected="selected">3.1</option>
        		                        					@else
        		                        						<option value="1" selected="selected">6.0</option>
            		                        					<option value="2">3.0</option>
            		                        					<option value="3">3.1</option>
        		                        					@endif
        		                        					
        		                        				</select>
        		                        			</div>
		                        				</div>
		                        				<div class="col-md-3">
		                        					<label class="col-md-3 control-label">Tipo: </label>
        		                        			<div class="col-md-9">
        		                        				<select id = "tipo_{{$i}}" name = "tipo_{{$i}}" class="form-control">
        		                        					@if($client->_count[$i]->tipo == 1)		                        					
        			                        					<option value="1" selected="selected">Consumo</option>
        			                        					<option value="2">Generación</option>
        			                        					<option value="3">Gas</option>
        			                        				@elseif($client->_count[$i]->tipo == 2)
        			                        					<option value="1">Consumo</option>
        			                        					<option value="2" selected="selected">Generación</option>
        			                        					<option value="3">Gas</option>
        			                        				@elseif($client->_count[$i]->tipo == 3)
        			                        					<option value="1">Consumo</option>
        			                        					<option value="2">Generación</option>
        			                        					<option value="3" selected="selected">Gas</option>
        			                        				@else
        			                        					<option value="1" selected="selected">Consumo</option>
        			                        					<option value="2">Generación</option>
        			                        					<option value="3">Gas</option>
        			                        				@endif		                        					
        		                        				</select>
        		                        			</div>
		                        				</div>
		                        				<div class="col-md-2">
		                        					<label class="col-md-6 control-label">Analizadores: </label>
        		                        			<div class="col-md-6">
        		                        				<?php 
        		                        					$aux_id = 'count_analizadores_'.$i;
        		                        				 ?>
        		                        				 @if(isset($analizadores[$i]))
        		                        					<input id="{{$aux_id}}" type="number" min="0" class="form-control" name="analizadores_{{$i}}" value="0">
        		                        				@else
        		                        					<input id="{{$aux_id}}" type="number" min="0" class="form-control" name="analizadores_{{$i}}" value="0">
        		                        				@endif
        		                        			</div>
		                        				</div>
		                        				<div class="col-md-4">
		                        					<div class="col-md-6">
        		                        				<button type="button" class="btn btn-success" style="width: 100%;" onclick="configurarAnalizador({{$i}});">Configurar Analizador</button>
        		                        			</div>
        		                        			<div class="col-md-6">
        		                        				<button type="button" class="btn btn-danger" style="width: 100%;" data-toggle="modal" data-target="#delete_contador_modal" onclick="borrarContador_ajax('{{$client->_count[$i]->id}}','{{$client->_count[$i]->count_label}}')">Borrar Contador</button>
        		                        			</div>
		                        				</div>
		                        			</div>		                        			
		                        		</div>
		                        		<div class = "col-md-12" style="margin-top:15px;">
            								<div class="row">
            									<div class="col-md-4">
                        							<label class="col-md-6 control-label">Grupo de Menús: </label>
                        							<div class="col-md-6">
                        								<select id="group_{{$i}}" name="groups[]" class="form-control group-selector">
                        									@foreach($grupos as $grupo)
                        										@if($grupo->id == $client->_count[$i]->group_id)
                        											<option value="{{$grupo->id}}" selected="selected">{{$grupo->nombre}}</option>
                        										@else
                        											<option value="{{$grupo->id}}">{{$grupo->nombre}}</option>
                        										@endif
                        									@endforeach
                        								</select>
                        							</div>
                        						</div>
                        						<div class="col-md-2 text-left">
                        							<button class="btn btn-primary btn-edit-groups" type="button">
                        								<span class="fa fa-edit"></span> Editar Grupos
                        							</button>
                        						</div>
            								</div>
            							</div>
		                        		<?php 
		                        			$div_analizadores_id = 'analizadores_'.$i;
		                        		 ?>
		                        		<div id="{{$div_analizadores_id}}">
		                        			<?php $k = 0; ?>
		                        			@if(isset($analizadores[$i]))
			                        			@foreach($analizadores[$i] as $value)
			                        				<?php $aux_label_id_analizador = 'id_analizador'.$k.'_contador_'.($i+1) ?>
			                        				<div id="{{$aux_label_id_analizador}}" class = "col-md-12 text-center" style="margin-top: 50px">
			                        					@if($k == 0)
			                        					<div class = "col-md-12 text-center">
			                        						<h4 style="color:#272822">Configuración del Analizador Principal (Contador {{$i+1}})</h4>
			                        						<br><br>
			                        					</div>
			                        					@else
			                        						<div class = "col-md-12 text-center">
			                        						<h4 style="color:#272822">Configuración del Analizador {{$k+1}} (Contador {{$i+1}})</h4>
			                        						<br><br>
			                        					</div>
			                        					@endif
			                        					<div class="col-md-12">
			                        						<label class="col-md-1 control-label">Nombre: </label>
			                        						<div class="col-md-3">
			                        							<input id = "name_analizador{{$k}}_contador_{{($i+1)}}" name = "name_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->label}}">
			                        						</div>
			                        						<label class="col-md-1 control-label">Host: </label>
			                        						<div class="col-md-3">
			                        							<input id = "val_host_analizador{{$k}}_contador_{{($i+1)}}" name = "val_host_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->host}}
			                        							">
			                        						</div>
			                        						<label class="col-md-1 control-label">DataBase: </label>
			                        						<div class="col-md-3">
			                        							<input id = "val_dbase_analizador{{$k}}_contador_{{($i+1)}}" name = "val_dbase_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->database}}">
			                        						</div>
			                        					</div>
			                        					<div class = "col-md-12"><br><br>
			                        						<label class="col-md-1 control-label">Port: </label>
			                        						<div class="col-md-2">
			                        							<input id = "val_port_analizador{{$k}}_contador_{{($i+1)}}" name = "val_port_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->port}}">
			                        						</div>
			                        						<label class="col-md-1 control-label">Username: </label>
			                        						<div class="col-md-3">
			                        							<input id = "val_username_analizador{{$k}}_contador_{{($i+1)}}" name = "val_username_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->username}}">
			                        						</div>
			                        						<label class="col-md-1 control-label">Password: </label>
			                        						<div class="col-md-3">
			                        							<input id = "val_password_analizador{{$k}}_contador_{{($i+1)}}" name = "val_password_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" value="{{$value->password}}">
			                        						</div>
			                        					</div>
			                        					<div class = "col-md-12">
			                        						<br><br>
			                        						<label class="col-md-1 control-label">Color etiqueta: </label>
			                        						<div class="col-sm-1">
			                        							<input id = "val_color_analizador{{$k}}_contador_{{($i+1)}}" name = "val_color_analizador{{$k}}_contador_{{($i+1)}}" class="form-control" type = "color" value="{{$value->color_etiqueta}}">
			                        						</div>
			                        						<div class="col-md-2">
			                        							<button type="button" class="btn btn-danger" style="width: 100%;" data-toggle="modal" data-target="#delete_analizador_modal" onclick="borrarAnalizador_ajax('{{$value->id}}','{{$value->label}}');">Borrar Analizador</button>
			                        						</div>
			                        						<br><br><br><br><br>
			                        					</div>
			                        				</div>
			                        				<?php  
			                        					$k++;
			                        				?>			                        				
			                        			@endforeach
			                        		@endif
		                        		</div>
	                        		</div>	                        		
	                        	@endfor
	                        	@include('User.modals.delete_contador')
	                        	@include('User.modals.delete_analizador')
	                        	<div class="col-md-12">
		                        	<div class="col-md-3">
		                        		<button class="btn btn-primary" onclick="agregarContador({{$contadores}})" type="button">Agregar Contador</button>
		                        		<input type="hidden" name="cantidad_new_cont" id="cantidad_new_cont" value="0">
		                        	</div>	                        		
	                        	</div>
	                        	<div class="col-md-12" id="new_contador">
	                        		
	                        	</div>
	                            {{--<label for="contadores" class="col-md-1 control-label">Contadores</label>

	                            <div class="col-md-2">
	                                <input id="contadores" readonly type="number" min="{{ $contadores }}" class="form-control" name="contadores" value="{{ $contadores }}" required>

	                                @if ($errors->has('contadores'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('contadores') }}</strong>
	                                    </span>
	                                @endif
	                            </div>--}}
	                        </div>


	                        <div class="form-group">
	                            <div class="col-md-11 col-md-offset-4">
	                                <button type="submit" class="btn color-127">
	                                    Guardar cambios
	                                </button>
	                            </div>
	                        </div>
	                    </form>
					</div>
				</div>
				@include('User.modals.groups')
				<div id="tmpl_contador" style="display:none;">
					<div class="col-md-12 text-center" style="margin-bottom: 60px;">
    					<h3 class="contador-title" style="color:#004165;"></h3>
    					<br>
    					<br>
    					<div class="col-md-12">
    						<label class="col-md-1 control-label">Nombre: </label>
    						<div class="col-md-3">
    							<input id="name_cont_###" name="name_cont_###" class="form-control">
    						</div>
    						<label class="col-md-1 control-label">Host: </label>
    						<div class="col-md-3">
    							<input id="val_host_###" name="val_host_###" class="form-control">
    						</div>
    						<label class="col-md-1 control-label">DataBase: </label>
    						<div class="col-md-3">
    							<input id="val_dbase_###" name="val_dbase_###" class="form-control">
    						</div>
    					</div>
    					<div class="col-md-12">
    						<br>
    						<br>
    						<label class="col-md-1 control-label">Port: </label>
    						<div class="col-md-2">
    							<input id="val_port_###" name="val_port_###" class="form-control">
    						</div>
    						<label class="col-md-1 control-label">Username: </label>
    						<div class="col-md-3">
    							<input id="val_username_###" name="val_username_###" class="form-control">
    						</div>
    						<label class="col-md-1 control-label">Password: </label>
    						<div class="col-md-3">
    							<input id="val_password_###" name="val_password_###" class="form-control">
    							<br>
    							<br>
    						</div>    						
    						<div class="col-md-12">
    							<div class="row" style="margin-top:15px;">
    								<div class="col-md-4">
    									<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
											<thead>
												<tr>
													<th>
														Tipo de contrato de Energía
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="text-center">
														<div class="radio-inline" style="margin-right:20px;">
                                                          	<label>
                                                          		<input type="radio" id="tipo_contrato_###_1" name="tipo_contrato_###" checked="checked" value="0">
                                                          		Precio Fijo
                                                          	</label>
                                                        </div>
                                                        <div class="radio-inline">
                                                          	<label>
                                                          		<input type="radio" id="tipo_contrato_###_2" name="tipo_contrato_###" value="1">
                                                          		Indexado
                                                          	</label>
                                                        </div>
													</td>
												</tr>
											</tbody>
										</table>
    								</div>
    								<div class="col-md-4">
    									<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
											<thead>
												<tr>
													<th>
														Perfil de Usuario
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="text-center">
														<div class="radio-inline" style="margin-right:20px;">
                                                        	<label>
                                                        		<input type="radio" name="tipo_usuario_0" id="tipo_usuario_###_1" checked="checked" value="0">
                                                        		Gestion (admin)
                                                          	</label>
                                                        </div>
                                                        <div class="radio-inline">
                                                          	<label>
                                                              	<input type="radio" name="tipo_usuario_0" id="tipo_usuario_###_2" value="1">
                                                                Técnico
                                                          	</label>
                                                        </div>
													</td>
												</tr>
											</tbody>
										</table>
    								</div>
    								<div class="col-md-4">
    									<table class="table table-bordered table-hover table-responsive table-blue table-stretch">
											<thead>
												<tr>
													<th>
														Logotipo Imagen Corporativa
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="text-center">
														<div class="row">
															<div class="col-md-5">
																 <div class="form-group">
																 	<input type="file" name="avatar_###" id="avatar_###" class="file-hidden">
																 	<label class="btn btn-primary" for="avatar_###"><span class="fa fa-upload"></span> Examinar</label>                                                                                                                                                        
                                                                  </div>
															</div>
															<div class="col-md-7">
																<span style="color:#000;">(Tamaño 360x360 pixeles)</span>
															</div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
    								</div>
    							</div>
    							<div class = "col-md-12" style="margin-top:15px;">
    								<div class="row">
    									<div class="col-md-3">
                							<label class="col-md-3 control-label">Tarifa: </label>
                							<div class="col-md-9">
                								<select id="tarifa_###" name="tarifa_###" class="form-control">
                									<option value="1">6.0</option>
                									<option value="2">3.0</option>
                									<option value="3">3.1</option>
                								</select>
                							</div>
                						</div>
                						<div class="col-md-3">
                							<label class="col-md-3 control-label">Tipo: </label>
                							<div class="col-md-9">
                								<select id="tipo_###" name="tipo_###" class="form-control">
                									<option value="1">Consumo</option>
                									<option value="2">Generación</option>
                									<option value="3">Gas</option>
                								</select>
                							</div>
                						</div>
                						<div class="col-md-2">
                							<label class="col-md-6 control-label">Analizadores: </label>
                							<div class="col-md-6">
                								<input id="count_analizadores_###" type="number" min="0" class="form-control contador-analizer" name="analizadores_###" value="0">
                							</div>
                						</div>
                						<div class="col-md-4">
                							<div class="row">
                								<div class="col-md-6">
                    								<button type="button" class="btn btn-success" style="width: 100%;" onclick="configurarAnalizador(###);">Configurar Analizador</button>
                    							</div>
                    							<div class="col-md-6">
                    								<button type="button" class="btn btn-danger" style="width: 100%;" onclick="borrarContador(###);">Borrar Contador</button>
                    							</div>
                							</div>
                						</div>
    								</div>
    							</div>
    							<div class = "col-md-12" >
        							<div class="row" style="margin-top:15px;">
    									<div class="col-md-4">
                							<label class="col-md-6 control-label">Grupo de Menús: </label>
                							<div class="col-md-6">
                								<select id="groups_###" name="groups[]" class="form-control group-selector">
                									@foreach($grupos as $grupo)
                										<option value="{{$grupo->id}}">{{$grupo->nombre}}</option>                										
                									@endforeach
                								</select>
                							</div>
                						</div>
                						<div class="col-md-2 text-left">
                							<button class="btn btn-primary btn-edit-groups" type="button">
                								<span class="fa fa-edit"></span> Editar Grupos
                							</button>
                						</div>
    								</div>
								</div>
    						</div>
    						<div id="analizadores_###"></div>
    					</div>
    				</div>
				</div>
			</div>
			<div class="copy">
	            <p> &copy; 2020 Submeter 4.0. Todos los derechos reservados</p>
			</div>
		</div>
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
	<script>
		function agregarContador(contadores)
		{
			var newContadores = $("[id^='new_contador_']");
			if(newContadores.length > 0) {
				var lastContador = $(newContadores[newContadores.length - 1]);
				var idcnt = lastContador.prop("id");
				idcnt = idcnt.split("_");
				idcnt = parseInt(idcnt[idcnt.length - 1]);
				contadores = idcnt + 1;
			}

			var tmpl = $("#tmpl_contador").html();
			tmpl = tmpl.replace(new RegExp('###', 'g'), contadores);
			tmpl = $(tmpl);
			tmpl.find(".contador-title").html("Datos de Contador " + (contadores + 1));
			tmpl.prop("id", "new_contador_" + contadores);
			$('#new_contador').append(tmpl);
			
			$("#cantidad_new_cont").val(contadores + 1);
			$(".btn-edit-groups").off("click").click(mostrarGrupos);
			//$('<div class = "col-md-12 text-center" style="margin-bottom: 60px;" id="new_contador_'+contadores+'"><h3 style="color:#004165;">Datos de Contador '+(contadores+1)+'</h3><br><br><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_cont_'+contadores+'" name = "name_cont_'+contadores+'" class="form-control" ></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host_'+contadores+'" name = "val_host_'+contadores+'" class="form-control"></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase_'+contadores+'" name = "val_dbase_'+contadores+'" class="form-control"></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port_'+contadores+'" name = "val_port_'+contadores+'" class="form-control"></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username_'+contadores+'" name = "val_username_'+contadores+'" class="form-control"></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password_'+contadores+'" name = "val_password_'+contadores+'" class="form-control"><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Logo: </label><div class="col-md-3"><input type="file" id = "avatar_'+contadores+'" name = "avatar_'+contadores+'" class="form-control"></div><label class="col-md-1 control-label">Tarifa: </label><div class="col-md-3"><select id = "tarifa_'+contadores+'" name = "tarifa_'+contadores+'" class="form-control"><option value=1>6.0</option><option value=2>3.0</option><option value=3>3.1</option></select><br><br></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Tipo: </label><div class="col-md-3"><select id = "tipo_'+contadores+'" name = "tipo_'+contadores+'" class="form-control"><option value=1>Consumo</option><option value=2>Generación</option><option value=3>Gas</option></select></div><label class="col-md-1 control-label">Analizadores: </label><div class="col-md-1"><input id="count_analizadores_'+contadores+'" type="number" min="0" class="form-control" name="analizadores_'+contadores+'" value="0"></div><div class="col-md-1"><button type="button" class="btn btn-success" style="width: 100%;" onClick="configurarAnalizador('+(contadores)+');">Configurar<br>Analizador</button></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarContador('+contadores+');">Borrar<br>Contador</button></div></div><div id=analizadores_'+contadores+'></div></div>').appendTo('#new_contador');
		}

		function borrarContador(id_C)
  		{
  			$('#new_contador_'+id_C).remove();
  		}

  		function borrarAnalizador(id_A,id_C)
  		{  			
  			console.log('#id_analizador'+id_A+'_contador_'+(parseInt(id_C+1)));
  			$('#id_analizador'+id_A+'_contador_'+(parseInt(id_C+1))).remove();
  		}

  		function configurarAnalizador(id) {  			
  			var num_counts = $('#count_analizadores_'+id).val();
  			console.log(num_counts,id);  			
  			for (var k = 0; k < num_counts; k++) {
  				j = k;
  				while($('#id_analizador'+j+'_contador_'+(id+1)+'').length)
  				{
  					j++;
  				}  				
  				i = j;
  			console.log('id_analizador'+i+'_contador_'+(id+1));
  				if(i == 0)
  				{
  					$('<div id="id_analizador'+i+'_contador_'+(id+1)+'" class = "col-md-12 text-center" style="margin-top: 50px;"><div class = "col-md-12 text-center"><h4 style="color:#272822">Configuración del Analizador Principal'+' (Contador '+(id+1)+')</h4><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_analizador'+i+'_contador_'+(id+1)+'" name = "name_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host_analizador'+i+'_contador_'+(id+1)+'" name = "val_host_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" name = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port_analizador'+i+'_contador_'+(id+1)+'" name = "val_port_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username_analizador'+i+'_contador_'+(id+1)+'" name = "val_username_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password_analizador'+i+'_contador_'+(id+1)+'" name = "val_password_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Color etiqueta: </label><div class="col-sm-1"><input id = "val_color_analizador'+i+'_contador_'+(id+1)+'" name = "val_color_analizador'+i+'_contador_'+(id+1)+'" class="form-control" type = "color"></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarAnalizador('+i+','+id+');">Borrar<br>Analizador</button></div><br><br><br><br><br></div></div>').appendTo('#analizadores_'+id);  					
  				}else{
  					$('<div id="id_analizador'+i+'_contador_'+(id+1)+'" class = "col-md-12 text-center"><div class = "col-md-12 text-center" id="id_analizador'+i+'_contador_'+(id+1)+'"><h4 style="color:#272822">Configuración del Analizador '+(i+1)+' (Contador '+(id+1)+')</h4><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_analizador'+i+'_contador_'+(id+1)+'" name = "name_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host_analizador'+i+'_contador_'+(id+1)+'" name = "val_host_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" name = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port_analizador'+i+'_contador_'+(id+1)+'" name = "val_port_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username_analizador'+i+'_contador_'+(id+1)+'" name = "val_username_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password_analizador'+i+'_contador_'+(id+1)+'" name = "val_password_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Color etiqueta: </label><div class="col-sm-1"><input id = "val_color_analizador'+i+'_contador_'+(id+1)+'" name = "val_color_analizador'+i+'_contador_'+(id+1)+'" class="form-control" type = "color"></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarAnalizador('+i+','+id+');">Borrar<br>Analizador</button></div><br><br><br><br><br></div></div>').appendTo('#analizadores_'+id);
  				}
  			}  		    
  		}

  		function borrarContador_ajax(id,contador_label)
  		{
  			console.log(contador_label);
  			$('#contador_id').val(id);
  			$('#contador_name').text(contador_label);
  		}

  		function borrarAnalizador_ajax(id,analizador_label)
  		{
  			console.log(id);
  			console.log(analizador_label);
  			$('#analizador_id').val(id);
  			$('#analizador_name').text(analizador_label);
  		}

  		$("#btn-eliminar-contador").click(function(event){
  	  		event.preventDefault();
			var contador_id = $("input[name=contador_id]").val();
			console.log(contador_id);
		 	$.ajax({
		 		type: "POST",
	            url: "{{route('eliminar.contador.ajax')}}",
	            data : {'contador_id':contador_id, '_token': $('.token-container input[name=_token]').val()},
	            success: function (data) {
	            	location.reload();
		            $('#delete_contador_modal').modal('hide');
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
	 	});

	 	$("#btn-eliminar-analizador").click(function(){
			var analizador_id = $("input[name=analizador_id]").val();
			console.log(analizador_id);
		 	$.ajax({
		 		type: "POST",
	            url: "{{route('eliminar.analizador.ajax')}}",
	            data : {'analizador_id':analizador_id, '_token': $('.token-container input[name=_token]').val()},
	            success: function (data) {
	            	location.reload();
	            	console.log(data);
		            $('#delete_analizador_modal').modal('hide');
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
	 	});

	 	
		$(document).ready(function(){
			$(".btn-return").click(function(event){
				event.preventDefault();
				window.history.back();
			});

			$(".btn-edit-groups").click(mostrarGrupos);
			$("#btnEditGroup").click(openGroup);
			$("#btnNewGroup").click(showNewGroup);
			$("#btnSaveGroup").click(showNewGroup);
			$("#btnDeleteGroup").click(deleteGroup);
			$("#formGroups").submit(sendGroup);			
		});

		function mostrarGrupos(event) {
			event.preventDefault();
			resetModalGroups();
			var form = $("#formGroups")[0];
			form.reset();
			getDataGroups();			
			$("#edit_groups").modal("show");
		}

		function showNewGroup(event) {
			event.preventDefault();
			var form = $("#formGroups")[0];
			form.reset();
			$("input[name='group_id']").val("");
			$(".group-data").show();
			$(".alert-groups").hide();
		}

		function openGroup(event) {
			event.preventDefault();
			var group = $("#groupSelector").val();
			if(!$.isNumeric(group)) {
				return false;
			}
			var form = $("#formGroups")[0];
			form.reset();
			var url = "{{url('group/')}}";
			url = url + "/" + group;
			$.ajax({
		 		type: "GET",
	            url: url,
	            success: function (data) {
	            	if(data.error) {
						return false;
	            	}
	            	$("#groupNameModal").val(data.group.nombre);
	            	$("input[name='group_id']").val(data.group.id);
	            	for(var i = 0; i < data.group.menus.length; i++) {
						$("input[name^='groupMenu[]'][value='" + data.group.menus[i] + "']").prop("checked", "checked");
	            	}
	            	$(".group-data").show();
	            	$(".alert-groups").hide();
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
		}

		function getDataGroups() {
			
			var url = "{{route('groups.get')}}";
			$.ajax({
		 		type: "GET",
	            url: url,
	            success: function (data) {
	            	if(data.error) {
						return false;
	            	}
	            	updateGroupSelector(data.groups);	            	
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
		}

		function sendGroup(event) {
			event.preventDefault();			
			var form = $("#formGroups")[0];
			var formData = new FormData(form);
			
			$.ajax({
		 		type: "POST",
		 		url: $("#formGroups").attr("action"),
		 		data: formData,
		 		cache: false,
			    contentType: false,
			    processData: false,
			    dataType: "json",
	            success: function (data) {
	            	updateGroupSelector(data.groups);
	            	$(".group-data").hide();
	            	$("#edit_groups .alert-groups.alert-success").show();
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
		}

		function deleteGroup(event) {
			event.preventDefault();
			var form = $("#formGroups");		
			var data = {
				_token : form.find("input[name='_token']").val(),
				_method : 'DELETE',
				group_id: form.find("input[name='group_id']").val(),
			};
			
			$.ajax({
		 		type: "POST",
		 		url: $("#formGroups").attr("action"),
		 		data: data,
		 		dataType: "json",
	            success: function (data) {
	            	updateGroupSelector(data.groups);
	            	$(".group-data").hide();
	            	$("#edit_groups .alert-groups.alert-danger").show();
	            },
	            error: function (data) {
	                console.log(data);
	            }	            
	        });
		}

		function resetModalGroups() {
			$("#btnEditGroup").hide();
			$(".group-data").hide();
			$(".alert-groups").hide();
		}

		function updateGroupSelector(groups) {
			var selectors = $(".group-selector");

			var options = '';
			for(var i = 0; i < groups.length; i++){
				options += '<option value="' + groups[i].id + '">' + groups[i].nombre + '</option>';
			}
			
			for(var i = 0; i < selectors.length; i++) {
				var selector = $(selectors[i]);
				var previousval = selector.val();
				selector.html(options);
				if($.isNumeric(previousval)) {
					selector.val(previousval);
				}
			}
			if($("#groupSelector option").length > 0){
				$("#btnEditGroup").show();
			}
		}

	 	
	</script>

@endsection