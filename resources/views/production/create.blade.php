@extends('Dashboard.layouts.global4')
@section('content')
	<div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
    	<form name="form-enterprise" id="form-fields" method="POST" action="{{ route('production.save') }}">
    		{{ csrf_field() }}
    		<input type="hidden" name="productionid" /> 
    		<input type="hidden" name="selecteddatabase" />
    		<input type="hidden" name="selectedtable" />
        	<div class="banner col-md-12 mb-4 mr-3">
        		<h3>Crear Configuración</h3>
        		<hr/>
        		<div class="row pr-3 mb-2">
        			<div class="col-12 text-right">
        				<button class="btn btn-success" type="submit"><span class="fa fa-save"></span> Guardar</button>
        			</div>
        		</div>
        		<div class="row pr-3">
            		<div class="col-4">
                    	<div class="form-group row">
                    		<label class="col-12 col-md-4 col-lg-3 text-left text-md-right pt-2">Nombre</label>
                    		<input type="text" name="name" value="" class="form-control col-12 col-md-8 col-lg-9" required="required" />
                    	</div>
                	</div>
                	<div class="col-4">
                    	<div class="form-group row">
                    		<label class="col-12 col-md-4 col-lg-3 text-left text-md-right pt-2">Empresa</label>
                    		<select class="form-control col-12 col-md-8 col-lg-9" name="enterprise">
                    			@foreach($enterprises as $enterprise)
                    				<option value="{{ $enterprise->id}}">{{$enterprise->name}}</option>
                    			@endforeach
                    		</select>
                    	</div>
                	</div>
                	<div class="col-4">
                    	<div class="form-group row">
                    		<label class="col-12 col-md-4 col-lg-3 text-left text-md-right pt-2">Contador</label>
							<select class="form-control col-12 col-md-8 col-lg-9 field-d" name="energymeter" data-value="{{old('energymeter')}}">
                                {{-- @foreach($energy_meters as $meter) Leo W ya no se va a usar                                  
                                        
                                   
                                        <option value="{{ $meter->id}}">{{$meter->count_label}}</option>
                                   
                                @endforeach --}}
                            </select>    
                    	</div>
                	</div>
				</div>
				{{-- Leo W los campos DB y tablas se pasan a cada operador                 --}}
                {{-- <div class="row pr-3">
                	<div class="col-2 text-center">
                		<button class="btn btn-primary" id="btnLoadDatabase">Cargar Tablas</button>
                	</div>
                
                	<div class="col-6">
                		<div class="form-group row">
                			<label class="col-5 text-left text-md-right pt-2">Base de Datos</label>
                    		  <select class="form-control col-7" name="database">
                        </select>                     		
                		</div>
                		<div class="text-right">
                    		<span class="help-block errorHelper" id="database_error" style="display:none;">
    	                    	<strong></strong>
    	                    </span>
	                    </div>
            		</div>
            		<div class="col-4">
                		<div class="form-group row">
                			<label class="col-5 text-left text-md-right pt-2">Tabla</label>
                    		<select class="form-control col-7" name="table">
                    		</select>
                		</div>
                		<div class="text-right">
                    		<span class="help-block errorHelper" id="table_error" style="display:none;">
    	                    	<strong></strong>
    	                    </span>
	                    </div>
            		</div>
                	
                </div> --}}
                <div class="row pr-6">
                	<div class="col-4">
                    	<div class="form-group row">
                    		<label class="col-12 col-md-4 col-lg-3 text-left text-md-right pt-2">Color</label>
                    		<input type="color" name="color" value="" class="form-control col-2 px-1 py-1" style="min-height:30px;" />
                    	</div>
					</div>
					{{-- @Leo W* se permite seleccionar el tipo de garfica en la configuracion --}}
					<div class="col-6">
                    	<div class="form-group row">
                    		<label class="col-12 col-md-6 col-lg-5 text-left text-md-right pt-2">Tipo de gr&aacute;fica</label>
                    		<select class="form-control col-12 col-md-6 col-lg-7" name="chart_type">
                    			@foreach($chartTypes as $id => $text)
                    				@if($id == old('chart_type'))
                    					<option value="{{ $id}}" selected="selected">{{$text}}</option>
                    				@else
                    					<option value="{{ $id}}">{{$text}}</option>
                    				@endif
                    			@endforeach
                    		</select>
                    	</div>
					</div>
				</div>
				<div class="row pr-3">
					{{-- @Leo W* dos nuevos campos en la configuracion de produccion --}}
					
					<div class="col-6">
                    	<div class="form-group row">
							<label class="col-12 col-md-6 col-lg-5 text-left text-md-right pt-2">Intervalo diario(minutos)</label>
							<select class="form-control col-12 col-md-6 col-lg-7" name="chart_interval_daily">
								<option value="15" {{old('chart_interval_daily') == 15 ? 'selected="selected"':''}}>15 minutos</option>
								<option value="30" {{old('chart_interval_daily') == 30 ? 'selected="selected"':''}}>30 minutos</option>
								<option value="45" {{old('chart_interval_daily') == 45 ? 'selected="selected"':''}}>45 minutos</option>
								<option value="60" {{old('chart_interval_daily') == 60 ? 'selected="selected"':''}}>1 Hora</option>
							</select>
                    	</div>
					</div>
					<div class="col-6">
                    	<div class="form-group row">
							<label class="col-12 col-md-6 col-lg-5 text-left text-md-right pt-2">Intervalo semanal(minutos)</label>
							<select class="form-control col-12 col-md-6 col-lg-7" name="chart_interval_weekly">
								<option value="15" {{old('chart_interval_weekly') == 15 ? 'selected="selected"':''}}>15 minutos</option>
								<option value="30" {{old('chart_interval_weekly') == 30 ? 'selected="selected"':''}}>30 minutos</option>
								<option value="45" {{old('chart_interval_weekly') == 45 ? 'selected="selected"':''}}>45 minutos</option>
								<option value="60" {{old('chart_interval_weekly') == 60 ? 'selected="selected"':''}}>1 Hora</option>
								<option value="120" {{old('chart_interval_weekly') == 120 ? 'selected="selected"':''}}>2 Horas</option>
								<option value="240" {{old('chart_interval_weekly') == 240 ? 'selected="selected"':''}}>4 Horas</option>
								<option value="360" {{old('chart_interval_weekly') == 360 ? 'selected="selected"':''}}>6 Horas</option>
								<option value="720" {{old('chart_interval_weekly') == 720 ? 'selected="selected"':''}}>12 Horas</option>
								<option value="1440" {{old('chart_interval_weekly') == 1440 ? 'selected="selected"':''}}>24 Horas</option>
							</select>
                    	</div>
                	</div>
                </div>
                <div class="row mt-3">
        			<div class="col-12 col-lg-6">
        				<div class="row mb-4">
                			<div class="col-8">
                				<h5>Campos Personalizados</h5>
                			</div>
                			<div class="col-4 text-left text-md-right">
                				<button class="btn btn-success btn-sm" type="button" id="btnAddField"><span class="fa fa-plus"></span> Agregar Campo</button>
                			</div>
                		</div>        		
                		<table class="table table-responsive table-striped bg-white mt-3" id="dtFields" data-index="0">
                          <thead class="bg-submeter-4">
                            <tr>
                              <th class="text-white" scope="col">Fields</th>
                              <th class="text-white" scope="col" width="35%">Nombre</th>
                              <th class="text-white" scope="col" width="15%">Operación</th>
                              <th class="text-white" scope="col">Editar</th>
                              <th class="text-white" scope="col">Remover</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                	</div>
        			<div class="col-12 col-lg-6">
        				<div class="row mb-4">
                			<div class="col-8">
                				<h5>Campos Agrupadores</h5>
                			</div>
                			<div class="col-4 text-left text-md-right">
                				<button class="btn btn-success btn-sm" type="button" id="btnAddFieldGroup"><span class="fa fa-plus"></span> Agregar Campo</button>
                			</div>
                		</div>        		
                		<table class="table table-responsive table-striped bg-white mt-3" id="dtFieldsGroup" data-index="0">
                          <thead class="bg-submeter-4">
                            <tr>
                              <th class="text-white" scope="col">Fields</th>
                              <th class="text-white" scope="col" width="35%">Nombre</th>
                              <th class="text-white" scope="col" width="35%">Nombre Mostrado</th>
                              <th class="text-white" scope="col" width="35%">Tipo</th>
                              <th class="text-white" scope="col" width="15%">Operación</th>
                              <th class="text-white" scope="col">Editar</th>
                              <th class="text-white" scope="col">Remover</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                	</div>
        		</div>
               
           </div>
       </form>
	</div>
	<template id="templateBtnDelete">
    	<button type="button" class="btn btn-danger"><span class="fa fa-times"></span></button>
    </template>
    <template id="templateBtnEdit">
    	<button type="button" class="btn btn-primary"><span class="fa fa-pen"></span></button>
    </template>
@include("production.include.fields_modal")
@include("production.include.fields_group_modal")
@endsection

@section('scripts')
	@include("production.include.scripts_fields");
@endsection