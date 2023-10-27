@extends('Dashboard.layouts.global5')

@section('counters')
	@include('Dashboard.includes.contadores', ["menuId" => 13])
@endsection

@section('content')
	@php
		$titulo = "&Aacute;rea Cliente";

		if (empty($tarifa->suministro_del_domicilio)){
			if (isset(Auth::user()->_perfil->domicilio_suministro)){
				$src = Auth::user()->_perfil->domicilio_suministro;
			}	
		} else {
			$src = $tarifa->suministro_del_domicilio;
		}
	@endphp
	<div class="row">	
		<div class="column">
			<div class="btn-container reverse">
				<button class="btn btn-danger btn-return"><i class="fa fa-times"></i><br/> Cancelar</button>
			</div>
		</div>
	</div>

	@if (Session::has('message'))
		<div class="row">
			<div class="column">
				<div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			</div>
		</div>
	@endif

	<div class="row row-xl">
		<div class="column col-65">
			<div class="table-container">
				<table id="table-area-cliente" class="table-responsive column-header text-left">
					<colgroup>
						<col class="shrink">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<td>
								Denominación social
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->denominacion_social))
										{!! Form::text('denominacion_social', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'denominacion_social']) !!}
									@else
										{!! Form::text('denominacion_social', Auth::user()->_perfil->denominacion_social, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'denominacion_social']) !!}
									@endif
								@else
									@if(empty($tarifa->denominacion_social))
										@if(!isset(Auth::user()->_perfil->denominacion_social))
											{!! Form::text('denominacion_social', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'denominacion_social']) !!}
										@else
											{!! Form::text('denominacion_social',Auth::user()->_perfil->denominacion_social, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'denominacion_social']) !!}
										@endif
									@else
										{!! Form::text('denominacion_social', $tarifa->denominacion_social, ['class' => 'form-control text-left',  'readonly', 'id' => 'denominacion_social']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Domicilio social
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->domicilio_social))
										{!! Form::text('social_domicilio', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'social_domicilio']) !!}
									@else
										{!! Form::text('social_domicilio',Auth::user()->_perfil->domicilio_social, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'social_domicilio']) !!}
									@endif
								@else
									@if(empty($tarifa->social_domicilio))
										@if(!isset(Auth::user()->_perfil->domicilio_social))
											{!! Form::text('social_domicilio', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'social_domicilio']) !!}
										@else
											{!! Form::text('social_domicilio',Auth::user()->_perfil->domicilio_social, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'social_domicilio']) !!}
										@endif
									@else
										{!! Form::text('social_domicilio', $tarifa->social_domicilio, ['class' => 'form-control text-left',  'readonly', 'id' => 'social_domicilio']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Domicilio del suministro
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->domicilio_suministro))
										{!! Form::text('suministro_del_domicilio', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'suministro_del_domicilio']) !!}
									@else
										{!! Form::text('suministro_del_domicilio',Auth::user()->_perfil->domicilio_suministro, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'suministro_del_domicilio']) !!}
									@endif
								@else
									@if(empty($tarifa->suministro_del_domicilio))
										@if(!isset(Auth::user()->_perfil->domicilio_suministro))
											{!! Form::text('suministro_del_domicilio', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'suministro_del_domicilio']) !!}
										@else
											{!! Form::text('suministro_del_domicilio',Auth::user()->_perfil->domicilio_suministro, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'suministro_del_domicilio']) !!}
										@endif
									@else
										{!! Form::text('suministro_del_domicilio', $tarifa->suministro_del_domicilio, ['class' => 'form-control text-left',  'readonly', 'id' => 'suministro_del_domicilio']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Latitud
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->latitud))
										{!! Form::text('latitud', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'latitud']) !!}
									@else
										{!! Form::text('latitud',Auth::user()->_perfil->latitud, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'latitud']) !!}
									@endif
								@else
									@if( isset($tarifa->LATITUD))
										{!! Form::text('latitud', $tarifa->LATITUD, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'latitud']) !!}
									@else
										{!! Form::text('latitud', null, ['class' => 'form-control text-left',  'readonly', 'id' => 'latitud']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Longitud
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->longitud))
										{!! Form::text('longitud', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'longitud']) !!}
									@else
										{!! Form::text('longitud', Auth::user()->_perfil->longitud, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'longitud']) !!}
									@endif
								@else
									@if( isset($tarifa->LONGITUD))
										{!! Form::text('longitud', $tarifa->LONGITUD, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'longitud']) !!}
									@else
										{!! Form::text('longitud', null, ['class' => 'form-control text-left',  'readonly', 'id' => 'longitud']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								CUPS
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->cups))
										{!! Form::text('cups', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cups']) !!}
									@else
										{!! Form::text('cups',Auth::user()->_perfil->cups, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cups']) !!}
									@endif
								@else
									@if(empty($tarifa->CUPS))
										@if(!isset(Auth::user()->_perfil->cups))
											{!! Form::text('cups', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cups']) !!}
										@else
											{!! Form::text('cups',Auth::user()->_perfil->cups, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cups']) !!}
										@endif
									@else
										{!! Form::text('cups', $tarifa->CUPS, ['class' => 'form-control text-left',  'readonly', 'id' => 'cups']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								CIF
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->cif))
										{!! Form::text('cif', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cif']) !!}
									@else
										{!! Form::text('cif',Auth::user()->_perfil->cif, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cif']) !!}
									@endif
								@else
									@if(empty($tarifa->CIF))
										@if(!isset(Auth::user()->_perfil->cif))
											{!! Form::text('cif', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cif']) !!}
										@else
											{!! Form::text('cif',Auth::user()->_perfil->cif, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'cif']) !!}
										@endif
									@else
										{!! Form::text('cif', $tarifa->CIF, ['class' => 'form-control text-left',  'readonly',  'id' => 'cif']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Empresa distribuidora
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->empresa_distribuidora))
										{!! Form::text('empresa_distribuidora', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_distribuidora']) !!}
									@else
										{!! Form::text('empresa_distribuidora',Auth::user()->_perfil->empresa_distribuidora, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_distribuidora']) !!}
									@endif
								@else
									@if(empty($tarifa->distribuidora_empresa))
										@if(!isset(Auth::user()->_perfil->empresa_distribuidora))
											{!! Form::text('empresa_distribuidora', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_distribuidora']) !!}
										@else
											{!! Form::text('empresa_distribuidora',Auth::user()->_perfil->empresa_distribuidora, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_distribuidora']) !!}
										@endif
									@else
										{!! Form::text('empresa_distribuidora', $tarifa->distribuidora_empresa, [ 'class' => 'form-control text-left',  'readonly',  'id' => 'empresa_distribuidora']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Empresa comercializadora
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->empresa_comercializadora))
										{!! Form::text('empresa_comercializadora', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_comercializadora']) !!}
									@else
										{!! Form::text('empresa_comercializadora',Auth::user()->_perfil->empresa_comercializadora, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_comercializadora']) !!}
									@endif
								@else
									@if(empty($tarifa->comercializadora_empresa))
										@if(!isset(Auth::user()->_perfil->empresa_comercializadora))
											{!! Form::text('empresa_comercializadora', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_comercializadora']) !!}
										@else
											{!! Form::text('empresa_comercializadora',Auth::user()->_perfil->empresa_comercializadora, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'empresa_comercializadora']) !!}
										@endif
									@else
										{!! Form::text('empresa_comercializadora', $tarifa->comercializadora_empresa, [ 'class' => 'form-control text-left',  'readonly',  'id' => 'empresa_comercializadora']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Persona de contacto
							</td>
							<td>
								@if(!isset($tarifa->contacto_persona))
									@if(!isset(Auth::user()->_perfil->persona_contacto))
										{!! Form::text('contacto_persona', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'contacto_persona']) !!}
									@else
										{!! Form::text('contacto_persona',Auth::user()->_perfil->persona_contacto, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'contacto_persona']) !!}
									@endif
								@else
									@if(empty($tarifa->contacto_persona))
										@if(!isset(Auth::user()->_perfil->persona_contacto))
											{!! Form::text('contacto_persona', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'contacto_persona']) !!}
										@else
											{!! Form::text('contacto_persona',Auth::user()->_perfil->persona_contacto, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'contacto_persona']) !!}
										@endif
									@else
										{!! Form::text('contacto_persona', $tarifa->contacto_persona, [ 'class' => 'form-control text-left',  'readonly',  'id' => 'contacto_persona']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Teléfono
							</td>
							<td>
								@if(!isset($tarifa->TELÉFONO))
									@if(!isset(Auth::user()->_perfil->fijo))
										{!! Form::text('telefono', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'telefono']) !!}
									@else
										{!! Form::text('telefono',Auth::user()->_perfil->fijo, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'telefono']) !!}
									@endif
								@else
									@if(empty($tarifa->TELÉFONO))
										@if(!isset(Auth::user()->_perfil->fijo))
											{!! Form::text('telefono', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'telefono']) !!}
										@else
											{!! Form::text('telefono',Auth::user()->_perfil->fijo, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'telefono']) !!}
										@endif
									@else
										{!! Form::text('telefono', $tarifa->TELÉFONO, [ 'class' => 'form-control text-left',  'readonly',  'id' => 'telefono']) !!}
									@endif
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Ayuda o contacto
							</td>
							<td>
								@if(!isset($tarifa->contacto_ayuda))
									{!! Form::text('contacto_ayuda', 'Plataforma Submeter 4.0', [ 'class' => 'form-control text-left',  'readonly',  'id' => 'contacto_ayuda']) !!}
								@else
									{!! Form::text('contacto_ayuda', $tarifa->contacto_ayuda, [ 'class' => 'form-control text-left',  'readonly',  'id' => 'contacto_ayuda']) !!}
								@endif
							</td>
						</tr>
						<tr>
							<td>
								Tarifa
							</td>
							<td>
								@if(!isset($tarifa))
									@if(!isset(Auth::user()->_perfil->tarifa))
										{!! Form::text('tarifa', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'tarifa']) !!}
									@else
										{!! Form::text('tarifa',Auth::user()->_perfil->tarifa, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'tarifa']) !!}
									@endif
								@else
									@if(empty($tarifa->TARIFA))
										@if(!isset(Auth::user()->_perfil->tarifa))
											{!! Form::text('tarifa', ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'tarifa']) !!}
										@else
											{!! Form::text('tarifa',Auth::user()->_perfil->tarifa, ['class' => 'form-control text-left',  'readonly', 'required', 'id' => 'tarifa']) !!}
										@endif
									@else
										{!! Form::text('tarifa', $tarifa->TARIFA, ['class' => 'form-control text-left',  'readonly',  'id' => 'tarifa']) !!}
									@endif
								@endif
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="column col-35">
			<div class="gmap-container">
				@if (isset($src))
					<iframe
						class="absolute w-full h-full border-none"
						src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDfonLBCRJDUX3k4Q81__hC6ZJcRIaeGnA&q={{ urlencode($src) }}">
					</iframe>
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<h3 class="title">Condiciones Actuales:</h3>
	</div>

	{!! Form::open(['route' => ['store.area.cliente', $user->id], 'method' => 'POST', 'id' => 'area_cliente', 'class' => 'wrapper', 'autocomplete' => 'off','files' => 'true','enctype'=>'multipart/form-data']) !!}
		<input type="hidden" name="tarifa" id="tarifa_send">
		<input type="hidden" name="telefono" id="telefono_send">
		<input type="hidden" name="cif" id="cif_send">
		<input type="hidden" name="cups" id="cups_send">
		<input type="hidden" name="contacto_persona" id="contacto_persona_send">
		<input type="hidden" name="empresa_distribuidora" id="empresa_distribuidora_send">
		<input type="hidden" name="empresa_comercializadora" id="empresa_comercializadora_send">
		<input type="hidden" name="suministro_del_domicilio" id="suministro_del_domicilio_send">
		<input type="hidden" name="social_domicilio" id="social_domicilio_send">
		<input type="hidden" name="denominacion_social" id="denominacion_social_send">
		<input type="hidden" name="contacto_ayuda" id="contacto_ayuda_send">
		<input type="hidden" name="latitud" id="latitud_send">
		<input type="hidden" name="longitud" id="longitud_send">
		<input type="hidden" value="{{ app('request')->input('contador') }}" name="contador">

		<div class="row">
			<div class="column">
				<div class="btn-container">
					<div>
						<label class="date_start" for="date_start">Fecha Inicio:</label>
						@if($precio_energia)
							{!! Form::text('date_start', $precio_energia[0]->date_start, ['id' => 'datepicker', 'required', 'disabled', 'class' => 'text-center']) !!}
						@else
						{!! Form::text('date_start',  ['id' => 'datepicker', 'required', 'class' => 'text-center']) !!}
						@endif
					</div>
					<button type="button" class="btn" onclick="resetDataConditions({{$user->id}})">NUEVAS CONDICIONES CONTRATO</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="column">
				<div class="table-container">
					<table id="table-condiciones-actuales" class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
							<col>
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>
									Potencia Contratada (kW)
								</th>
								<th>
									Precio Energía (€/kWh)
								</th>
								<th>
									Precio Potencia (€/kW*mes)
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $tama = count($potencia_contratada); ?>
							@for($i=1; $i <= $tama; $i++)
								<tr>
									<td>
										P{{$i}}
										{!! Form::hidden('perido'.$i, 'P'.$i) !!}
									</td>
									<td>
										@if(empty($potencia_contratada) || !isset($potencia_contratada[$i - 1]))
											{!! Form::text('contratada'.$i, [ 'required', 'onkeyup' => 'replace(this)', 'id' => 'contratada'.$i, 'class' => 'text-center']) !!}
										@else
											{!! Form::text('contratada'.$i, number_format($potencia_contratada[$i - 1]->Potencia_contratada,0,',','.'), [ 'readonly', 'onkeyup' => 'replace(this)', 'id' => 'contratada'.$i, 'class' => 'text-center']) !!}
										@endif
									</td>
									<td>
										@if(empty($precio_energia) || !isset($precio_energia[$i - 1]))
											{!! Form::text('energia'.$i, [ 'required', 'onkeyup' => 'replace(this)', 'class' => 'text-center']) !!}
										@else
											{!! Form::text('energia'.$i, number_format($precio_energia[$i - 1]->precio,6,',','.'), [ 'readonly', 'onkeyup' => 'replace(this)', 'class' => 'text-center']) !!}
										@endif
									</td>
									<td>
										@if(empty($precio_potencia) || !isset($precio_potencia[$i - 1]))
											{!! Form::text('potencia'.$i, [ 'required', 'onkeyup' => 'replace(this)', 'class' => 'text-center']) !!}
										@else
											{!! Form::text('potencia'.$i, number_format($precio_potencia[$i - 1]->Precio,6,',','.'), [ 'readonly', 'onkeyup' => 'replace(this)', 'class' => 'text-center']) !!}
										@endif
									</td>
								</tr>
							@endfor
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="column col-25">
				<div class="table-container">
					<table class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>
									Impuesto Especial Elétrico (I.E.E)
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									Impuesto Eléctrico
								</td>
								<td>
									{!! Form::text('impuesto', '5,11 %', ['class' => 'form-control text-center', 'readonly', 'onkeyup' => 'replace(this)']) !!}
								</td>
							</tr>
							<tr>
								<td>
									Reducción Base Imponible
								</td>
								<td class="text-center">
									@if($iee_cont == 2)
										<input type="radio" name="reduccion" value="0"> 0 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="85" checked = "checked"> 85 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="100"> 100 %·I.E.E
									@elseif($iee_cont == 3)
										<input type="radio" name="reduccion" value="0"> 0 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="85"> 85 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="100" checked = "checked"> 100 %·I.E.E
									@else
										<input type="radio" name="reduccion" value="0" checked = "checked"> 0 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="85"> 85 %·I.E.E &nbsp;
										<input type="radio" name="reduccion" value="100"> 100 %·I.E.E
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="table-container">
					<table class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>
									Alquiler Equipo Medida
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									Equipo de Medida (€/dia)
								</td>
								<td>
									@if(empty($alquiler_equipo_medida) || !isset($alquiler_equipo_medida[0]))
										{!! Form::text('equipo_medida',  ['class' => 'form-control text-center',  'required', 'readonly', 'onkeyup' => 'replace(this)']) !!}
									@else
										{!! Form::text('equipo_medida', number_format($alquiler_equipo_medida[0]->Alquiler_Equipo_Medida,2,',','.'), ['class' => 'form-control text-center', 'readonly', 'onkeyup' => 'replace(this)']) !!}
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="table-container">
					<table class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>
									Factor Emisiones CO2
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									Factor (kgCO2eq/kWh)
								</td>
								<td>
									@if($factor)
										{!! Form::text('factor', number_format($factor[0]->coeficiente,'5',',','.'), ['class' => 'form-control text-center', 'readonly','onkeyup' => 'replace(this)']) !!}
									@else
										{!! Form::text('factor',  ['class' => 'form-control text-center', 'required', 'onkeyup' => 'replace(this)']) !!}
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="column col-25">
				<div class="table-container">
					<table class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>
									Objetivo (kWh / semana)
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data_linea_base["values_linea"] as $linea)
								<tr>
									<td>{{$linea->name}}</td>
									<td class="text-center">
										<input type="hidden" name="dias_linea[]" value="{{ $linea->dia }}" />
										<input type="text" name="potencias_linea[]" class="form-control text-center value-linea" value="{{$linea->potencia_linea}}" />
									</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr class="row-header">
								<td colspan="2">								
									<div>
										Fecha de Inicio <br/> (Linea Base):
									</div>
									<div>
										<input type="text" class="datepicker text-center" name="fecha_inicio_linea_base" value='{{ $data_linea_base["fecha_inicio_linea_base"] }}' />
									</div>								
								</td>
							</tr>
							<tr class="row-header">
								<td colspan="2">								
									<div>
										Fecha de Fin <br/> (Linea Base):
									</div>
									<div>
										<input type="text" class="datepicker text-center" name="fecha_fin_linea_base" value='{{ $data_linea_base["fecha_fin_linea_base"] }}' />
									</div>								
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>	
			<div class="column col-25">
				<div class="table-container">
					<table class="table-responsive text-center">
						<colgroup>
							<col class="shrink">
							<col>
						</colgroup>
						<thead>
							<tr class="row-header">
								<th></th>
								<th>Costes representación (€ / kWh)</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data_representacion["costes"] as $index => $coste)
							<tr>
								<td>P{{$index + 1}}</td>
								<td class="text-center">
									<input type="text" name="costes_representacion[]" class="form-control text-center" value="{{number_format($coste,6,',','.')}}" />
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr class="row-header">
								<td colspan="2">								
									<div>
										Fecha de Inicio <br/> (Representación):
									</div>
									<div>
										<input type="text" class="datepicker text-center" name="fecha_inicio_representacion" value='{{ $data_representacion["fecha_inicio_representacion"] }}'/>
									</div>								
								</td>
							</tr>
							<tr class="row-header">
								<td colspan="2">								
									<div>
										Fecha de Fin <br/> (Representación):
									</div>
									<div>
										<input type="text" class="datepicker text-center" name="fecha_fin_representacion" value='{{ $data_representacion["fecha_fin_representacion"] }}'/>
									</div>								
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="column col-25">
				<div class="card shadow">
					<div class="card__header">
						<div class="card__title text-center">Tipo de cotrato de Energ&iacute;a</div>
					</div>
					<div class="card__body">
						<div>
							<br><label>
								<input type="radio" name="tipo_contrato" disabled='disabled' @if($data_cliente->tipo_contrato != 1) checked="checked" @endif>
								Precio Fijo
							</label><br>
							<label><br>
								<input type="radio" name="tipo_contrato" disabled='disabled' @if($data_cliente->tipo_contrato == 1) checked="checked" @endif>
								Indexado
							</label><br><br>
						</div>
					</div>
				</div>
			</div>	
			<div class="column col-25">
				<div class="card shadow">
					<div class="card__header">
						<div class="card__title text-center">Perfil de Usuario</div>
					</div>
					<div class="card__body">
						<div>
							<br><label>
								<input type="radio" name="tipo_cliente" disabled='disabled' @if($data_cliente->tipo_usuario != 1) checked="checked" @endif>
								Gestion (admin)
							</label><br>
							<label><br>
								<input type="radio" name="tipo_cliente" disabled='disabled' @if($data_cliente->tipo_usuario == 1) checked="checked" @endif>
								Técnico
							</label><br><br>
						</div>
					</div>
				</div>
			</div>	
			<div class="column col-25">
				<div class="card shadow">
					<div class="card__header">
						<div class="card__title text-center">Logotipo Imagen Corporativa</div>					
					</div>
					<div class="card__body">
						<img src="{{asset( $dir_image_count )}}" alt="Logo" style="width: 125px">
						<input type="file" name="file_logo" id="fileImage" class="d-none">
						<p>(Tamaño 360x360 pixeles)</p>
						<label class="btn btn-info btn-sm" for="fileImage">
							<i class="fa fa-upload"></i> Examinar
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="column">
				<div class="btn-container center">
					{!! Form::submit('Guardar', array('class' => 'btn', 'onclick' => 'asignar_tarifa()')) !!}
				</div>
			</div>
		</div>
	{!! Form::close() !!}								
@endsection
@section('scripts')
	<script src="{{asset('js/bootstrap.min.js')}}"> </script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
	<script>
		$(document).ready(function(){
			$(".value-linea").keydown(forzarEntero);
			$(".value-linea").keyup(function(){
				var obj = $(event.currentTarget);
				formatearLineas(obj);
			});

			var valuesLinea = $(".value-linea");
			for(var i = 0; i < valuesLinea.length; i++)
			{
				var obj = $(valuesLinea[i]);
				formatearLineas(obj);
			}

			$(".btn-return").click(function(event){
				event.preventDefault();
				window.history.back();
			});

			$( ".datepicker" ).datepicker({
				dateFormat:'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
			});
		});

		function forzarEntero(event)
		{
			var regexp = /^\d?[.]?$/;
			var key = event.originalEvent.key;
			if(key.length == 1 && !regexp.test(key) && !event.originalEvent.ctrlKey)
			{
				return false;
			}
		}

		function formatearLineas(obj)
		{
			var val = obj.val();
			if(val.length == 0)
			{
				return false;
			}
			val = val.replace(/[.]/g, "");
			val = val.replace(",", ".");
			val = parseFloat(val);
			if(isNaN(val))
			{
				obj.val("");
				return false;
			}
			obj.val(val.toLocaleString("es"));
		}

	  $( function(){
	    $( "#datepicker" ).datepicker({
	    	dateFormat:'yy-mm-dd',
	    	changeMonth: true,
				changeYear: true,
	    });
	  });

	  function asignar_tarifa(){
	  	var tari = $('#tarifa').val();
	  	$('#tarifa_send').val(tari);
	  	$('#telefono_send').val($('#telefono').val());
	  	$('#cif_send').val($('#cif').val());
	  	$('#cups_send').val($('#cups').val());
	  	$('#contacto_persona_send').val($('#contacto_persona').val());
	  	$('#empresa_distribuidora_send').val($('#empresa_distribuidora').val());
	  	$('#empresa_comercializadora_send').val($('#empresa_comercializadora').val());
	  	$('#suministro_del_domicilio_send').val($('#suministro_del_domicilio').val());
	  	$('#social_domicilio_send').val($('#social_domicilio').val());
	  	$('#denominacion_social_send').val($('#denominacion_social').val());
	  	$('#contacto_ayuda_send').val($('#contacto_ayuda').val());
	  	$('#latitud_send').val($('#latitud').val());
	  	$('#longitud_send').val($('#longitud').val());
	  }

	  function resetDataConditions(id){
	  	// var tipo_tarifa = <?php echo json_encode(number_format($tipo_tarifa)) ?>;
	  	var tipo_tarifa = @php echo json_encode(number_format($tipo_tarifa)) @endphp;
	  	if(tipo_tarifa == 1)
	  	{
		  	for (var i = 1; i <= 6; i++) {
		  		var aux = 0;
		  		aux = $('#contratada'+i).val().replace(/\./g, '');
		  		$('input[name="contratada'+i+'"]').val(aux).attr('readonly', false);
		  		$('input[name="energia'+i+'"]').attr('readonly', false);
		  		$('input[name="potencia'+i+'"]').attr('readonly', false);
		  	}
		  	console.log('tarifa1');
	  	} else {
	  		for (var i = 1; i <= 3; i++) {
		  		var aux = 0;
		  		aux = $('#contratada'+i).val().replace(/\./g, '');
		  		$('input[name="contratada'+i+'"]').val(aux).attr('readonly', false);
		  		$('input[name="energia'+i+'"]').attr('readonly', false);
		  		$('input[name="potencia'+i+'"]').attr('readonly', false);
		  	}
			}

	  	$('input[name="tarifa"]').attr('readonly', false);
	  	$('input[name="telefono"]').attr('readonly', false);
	  	$('input[name="cif"]').attr('readonly', false);
	  	$('input[name="cups"]').attr('readonly', false);
	  	$('input[name="contacto_persona"]').attr('readonly', false);
	  	$('input[name="empresa_distribuidora"]').attr('readonly', false);
	  	$('input[name="empresa_comercializadora"]').attr('readonly', false);
	  	$('input[name="suministro_del_domicilio"]').attr('readonly', false);
	  	$('input[name="social_domicilio"]').attr('readonly', false);
	  	$('input[name="denominacion_social"]').attr('readonly', false);
	  	$('input[name="contacto_ayuda"]').attr('readonly', false);
	  	$('input[name="date_start"]').attr('disabled', false);
	  	$('input[name="equipo_medida"]').attr('readonly', false);
	  	$('input[name="factor"]').attr('readonly', false);
	  	$('input[name="latitud"]').attr('readonly', false);
	  	$('input[name="longitud"]').attr('readonly', false);
	  }

		function replace(element) {
		  // set temp value
		  var tmp = element.value;
		  // replace everything that's not a number or comma or decimal
		  tmp = tmp.replace(/[^0-9,.]/g, "");
		  // replace commas with decimal
		  tmp = tmp.replace(/\./g, ',');
		  // set element value to new value
		  element.value = tmp;
		}
	</script>
@endsection
