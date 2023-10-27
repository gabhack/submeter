@extends('Dashboard.layouts.global4')

@section('content')
<div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
	<form name="form-enterprise" id="form-enterprise" method="POST" action="{{ route('enterprise.update', ['enterprise_id' => $empresa->id]) }}">
		{{ csrf_field() }} 
		<input type="hidden" name="_method" value="PATCH" />
		<input type="hidden" name="id" value="{{ $empresa->id }}" />
		<div class="banner col-md-12 mb-4 mr-3">
			<h3>Editar Empresa</h3>
			<hr/>    		
			<div class="row">
				<div class="col-12">
					<div class="form-group row">
						<label class="col-12 col-md-3 col-lg-2 text-left text-md-right pt-2">Nombre</label>
						<input type="text" name="name" value="{{ $empresa->name }}" class="form-control col-10 offset-1 offset-md-0 col-md-5" required="required" />
					</div>
				</div>
			</div>
			<div class="d-none">
				<div id="iptEnergy"></div>
				<div id="iptAnalyzer"></div>
				<div id="iptGroupAnalyzer">
					@foreach($empresa->groups_analyzer as $gAnalyzer)
						<input type="hidden" name="analyzerGroups[]" value="{{$gAnalyzer->analyzer_group_id}}" />
					@endforeach
				</div>
				<div id="iptUser"></div>
				<div id="iptUserEnergy">
					@foreach($empresa->enterprise_users as $enterprise_user)
						@foreach($enterprise_user->user->counts as $count)
							<input type="hidden" name="userEnergy[{{$enterprise_user->user_id}}][]" value="{{$count->meter_id}}" />
						@endforeach
					@endforeach
				</div>
				<div id="iptUserAnalyzer">
					@foreach($empresa->enterprise_users as $enterprise_user)
						@foreach($enterprise_user->user->analyzers as $analyzer)
							<input type="hidden" name="userAnalyzer[{{$enterprise_user->user_id}}][]" value="{{$analyzer->analyzer_id}}" />
						@endforeach
					@endforeach
				</div>
				<div id="iptUserGroup">
					@foreach($empresa->enterprise_meters as $enterprise_meter)
						@foreach($empresa->enterprise_users as $enterprise_user)
							@foreach($enterprise_user->user->groups as $group)
								@if($group->meter_id == $enterprise_meter->meter_id)
									<input type="hidden" data-energy="{{$group->meter_id}}" name="userGroup[{{$enterprise_user->user_id}}][{{$group->meter_id}}]" value="{{$group->group_id}}" />
								@endif
							@endforeach            				
						@endforeach
					@endforeach
				</div>
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
						<tbody> 
							@foreach($empresa->enterprise_meters as $enterprise_meter)                 			
								@if(is_object($enterprise_meter))
									<tr>
										<td>{{$enterprise_meter->meter->id}}</td>
										<td>{{$enterprise_meter->meter->count_label}}</td>
										<td></td>
										<td></td>
									</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				</div>            	
				<div class="col-12 col-lg-6 mt-3 mt-lg-0">
					<div class="row mb-4">
						<div class="col-12 col-md-12">
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
								<th class="text-white" scope="col">Etiqueta</th>  
								<th class="text-white" scope="col">Contador</th>
								<th class="text-white" scope="col">Editar</th>                    
								<th class="text-white" scope="col">Remover</th>
							</tr>
						</thead>
						<tbody>
							@foreach($empresa->enterprise_analyzers as $enterprise_analyzer)
								<tr>
									<td>{{$enterprise_analyzer->analyzer->id}}</td>
									<td>{{$enterprise_analyzer->analyzer->label}}</td>
									<td>{{$enterprise_analyzer->analyzer->meters()->first()->count_label ?? 0}}</td>
									<td></td>
									<td></td>                      				
								</tr>
							@endforeach
						</tbody>
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
						<div class="col-12 text-left text-md-right">
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
								<th class="text-white" scope="col" width="15%">Grupos</th>
								<th class="text-white" scope="col" width="15%">No. Dispositivos</th>
								<th class="text-white" scope="col">Dispositivos</th>
								<th class="text-white" scope="col">Grupo</th>
								<th class="text-white" scope="col">Remover</th>
							</tr>
						</thead>
						<tbody>
							@foreach($empresa->enterprise_users as $enterprise_user)
								<tr>
									<td>{{$enterprise_user->user->id}}</td>
									<td>{{$enterprise_user->user->name}}</td>
									<td>{{$enterprise_user->user->email}}</td>
									<td>C{{ count($enterprise_user->user->groups) }}</td>
									<td>Contadores:{{ count($enterprise_user->user->counts) }}
										<br/>
										Analizadores:{{ count($enterprise_user->user->analyzers) }}
									</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-lg-6 col-sm-12">
					<div data-statistic-config-list="produccion" 
						data-base-url="{{url('')}}"
						data-emp-id="{{$empresa->id}}"
						data-back-url="{{Auth::user()->tipo == 1 ? '':  '' }}">
					</div>   
				</div>
				<div class="col-lg-6 col-sm-12">
					<div data-statistic-config-list="indicadores"
						data-base-url="{{url('')}}"
						data-emp-id="{{$empresa->id}}"
						data-back-url="{{Auth::user()->tipo == 1 ? '':  '' }}">
					</div>   
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-12">
					<div data-manual 
						data-enterprise-id="{{$empresa->id}}"
						data-user-level="{{$user->tipo}}"
						data-base-url="{{url('')}}"
						data-back-url="{{url()->previous()}}">
					</div>
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
