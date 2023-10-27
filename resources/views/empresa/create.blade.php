@extends('Dashboard.layouts.global4')

@section('content')
<div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
	<form name="form-enterprise" id="form-enterprise" method="POST" action="{{ route('enterprise.save') }}">
		{{ csrf_field() }} 
    	<div class="banner col-md-12 mb-4 mr-3">
    		<h3>Crear Empresa</h3>
    		<hr/>
    		
    		<div class="row">
        		<div class="col-12">
                	<div class="form-group row">
                		<label class="col-12 col-md-3 col-lg-2 text-left text-md-right pt-2">Nombre</label>
                		<input type="text" name="name" value="" class="form-control col-10 offset-1 offset-md-0 col-md-5" required="required" />
                	</div>
            	</div>
            </div>
            <div class="d-none">
            	<div id="iptEnergy"></div>
            	<div id="iptAnalyzer"></div>
            	<div id="iptGroupAnalyzer"></div>
            	<div id="iptUser"></div>
            	<div id="iptUserEnergy"></div>
            	<div id="iptUserAnalyzer"></div>
            	<div id="iptUserGroup"></div>
            </div>
            <hr/>
        	<div class="row">
            	<div class="col-12 col-lg-6">
            		<div class="row mb-4">
            			<div class="col-6 col-md-8">
            				<h5>Contadores</h5>
            			</div>
            			<div class="col-6 col-md-4 text-left text-md-right">
            				<button class="btn btn-success btn-sm" type="button" id="btnContadores"><span class="fa fa-plus"></span> Asignar Contador</button>
            			</div>
            		</div>
            		<table class="table table-responsive table-striped bg-white" id="dtAssignEnergy">
                      <thead class="bg-submeter-4">
                        <tr>
                          <th class="text-white" scope="col">ID</th>
                          <th class="text-white" scope="col" width="100%">Etiqueta</th>
                          <th class="text-white" scope="col">Editar</th>
                          <th class="text-white" scope="col">Remover</th>                      
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
            	</div>
            	<div class="col-12 col-lg-6 mt-3 mt-lg-0">
    				<div class="row mb-4">
            			<div class="col-6 col-md-8">
            				<h5>Analizadores</h5>
            			</div>
            			<div class="col-12 col-md-12 text-left text-md-right">
            				<button class="btn btn-success btn-sm" type="button" id="btnGroupsAnalyzers"><span class="fa fa-check"></span> Grupos de Analizadores</button>
            				<button class="btn btn-success btn-sm" type="button" id="btnAnalyzers"><span class="fa fa-plus"></span> Asignar Analizador</button>
            			</div>
            		</div>        		
            		<table class="table table-striped bg-white mt-3" id="dtAssignAnalyzer">
                      <thead class="bg-submeter-4">
                        <tr>
                          <th class="text-white" scope="col">ID</th>
                          <th class="text-white" scope="col">Contador</th>
                          <th class="text-white" scope="col">Etiqueta</th>
                          <th class="text-white" scope="col">Editar</th>
                          <th class="text-white" scope="col">Remover</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
            	</div>
    		</div>
    		<hr/>
    		<div class="row mt-3">
    			<div class="col-12 col-lg-8 offset-lg-2">
    				<div class="row mb-4">
            			<div class="col-8">
            				<h5>Usuarios</h5>
            			</div>
            			<div class="col-4 text-left text-md-right">
            				<button class="btn btn-primary btn-sm" type="button" id="btnGroups"><span class="fa fa-check"></span> Editar Grupos</button>
            				<button class="btn btn-success btn-sm" type="button" id="btnUsers"><span class="fa fa-plus"></span> Asignar Usuario</button>
            			</div>
            		</div>        		
            		<table class="table table-responsive table-striped bg-white mt-3" id="dtAssignUser">
                      <thead class="bg-submeter-4">
                        <tr>
                          <th class="text-white" scope="col">ID</th>
                          <th class="text-white" scope="col" width="35%">Nombre</th>
                          <th class="text-white" scope="col" width="35%">Email</th>
                          <th class="text-white" scope="col" width="15%">Grupo</th>
                          <th class="text-white" scope="col" width="15%">Dispositivos</th>
                          <th class="text-white" scope="col">Contadores</th>
                          <th class="text-white" scope="col">Grupo</th>
                          <th class="text-white" scope="col">Remover</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
            	</div>
    		</div>
    		<div class="row mt-3">
    			<div class="col-12 text-md-right">
    				<button class="btn btn-primary"><span class="fa fa-save"></span> Guardar</button>
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
<template id="templateBtnUserEnergy">
	<button type="button" class="btn btn-primary"><span class="fa fa-clock"></span></button>
</template>
<template id="templateBtnUserGroup">
	<button type="button" class="btn btn-success"><span class="fa fa-check"></span></button>
</template>
@include("empresa.modal.energy")
@include("empresa.modal.analyzers")
@include("empresa.modal.user")
@include("empresa.modal.energyuser")
@include("empresa.modal.group")
@include("empresa.modal.groupuser")
@include("empresa.modal.analyzergroups")
@endsection

@section('scripts')
	@include("empresa.modal.modalscripts");
@endsection