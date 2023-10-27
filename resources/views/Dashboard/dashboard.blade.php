@extends('Dashboard.layouts.global5')

@section('intervals')
	@include('Dashboard.includes.intervalos')
@endsection
		
@section('content')

	<?php
		$array_Energia_Activa = array();
		$array_Energia_Reactiva = array();
		$array_Potencia_Contratada = array();
		$array_Exceso_Potencia = array();
		$array_IEE = array();
		$array_Equipo_Medida = array();
		$array_IVA = array();
	?>

	<div class="d-none">
		<div class="pdf-header" style="margin-bottom:-50px;">
			<div class="container" style="width:100%; display: inline-block">
				<div class="row">
					<div class="col">
						<img class="float-left" width="60" height="60" src="{{asset($dir_image_count)}}">
					</div>
					<div class="col">
						<h5 style="text-align: center;">Resumen de Contadores<h5>
					</div>
					<div class="col">
						<img class="float-right" width="60" height="60" src="{{asset('images/Logo_WEB_Submeter.png')}}">
					</div>
				</div>
			</div>
			<div>
				<p class="text-left" id="pdf_encabezado" style="font-family:'Univers-45-Light'; font-size: 14px;"><strong>{{$user->name}}:</strong> Desde {{$date_from}} hasta {{$date_to}}</p>
				<table class="table table-bordered table-striped table-hover table-condensed table-responsive tabla1 table-analisis-comparacion " id="tabla2" style="width: 100%; margin: 0px auto">
					<tr style="background-color: #2e6da4;">
						<th class="text-center" style="color: #fff;">Contador</th>
						<th class="text-center" style="color: #fff;">Tarifa</th>
						<th class="text-center" style="color: #fff;">CUPS</th>
						<th class="text-center" style="color: #fff;">Nombre</th>
						<th style="color: #fff;">Dirección</th>
					</tr>
					@for ($i = 0; $i < $contador_count ; $i++)
						<tr>
							<td class="text-center text-nowrap" style="color: #004165;">C{{$i+1}}</td>
							<td class="text-center text-nowrap" style="color: #004165;"><strong>{{$contador_tarifa[$i]}}</strong></td>
							<td class="text-left text-nowrap" style="color: #004165;">{{$contador_cups[$i]}}</td>
							<td class="text-left" style="color: #004165;"><strong>{{$contador_name[$i]}}</strong</td>
							<td class="text-left" style="color: #004165; font-size: 12px;">{{$contador_direccion[$i]}}</td>
						</tr>
					@endfor
				</table><br><br>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="column col-65">
			<div class="table-container">
				<table class="table-responsive table-striped">
					<thead>
						<tr class="row-header">
							<th>Contador</th>
							<th>Tarifa</th>
							<th>CUPS</th>
							<th>Nombre</th>
							<th>Dirección</th>
						</tr>
					</thead>
					<tbody>
						@for ($i = 0; $i < $contador_count ; $i++)
							<tr>
								<td class="text-center">C{{$i+1}}</td>
								<td><strong>{{$contador_tarifa[$i]}}</strong></td>
								<td>{{$contador_cups[$i]}}</td>
								<td><strong>{{$contador_name[$i]}}</strong></td>
								<td>{{$contador_direccion[$i]}}</td>
							</tr>
						@endfor
					</tbody>
				</table>
			</div>
		</div>
		<div class="column col-35">
			<div id="map" class="gmap-container"></div>
		</div>
	</div>

	<div class="row mt-content">
		<div class="flex flex-wrap m-auto mt-content">
			<div class="chart-container">
				<div id="pie_total"></div>
			</div>
			<div class="chart-container">
				<div id="pie_energia"></div>
			</div>
			<div class="chart-container">
				<div id="pie_potencia"></div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="column">
			<div class="wrapper-lg">
				<div class="table-container">
					<table class="table-responsive" id="tabla2">
						<thead>
							<tr class="row-header">
								<th>Concepto</th>
								{{-- <th>Cálculo</th> --}}
								<th>Importe</th>
							</tr>
						</thead>
						<tbody>
							<tr class="row-highlight">
								<td>
									<label>Término Energía</label>
								</td>
								<td>
									<?php
										$total1 = 0;
										$index = 0;
										foreach ($coste_activa as $key1) {
											$aux_index = 'costeP';
											foreach ($key1[0] as $key1a) {
												$total1 = $total1 + $key1a;
											}
										}
									?>
									<?php
										$total2 = 0;
										$index = 0;
										$aux_index = 'costeP';
										foreach ($coste_reactiva as $key2) {
											foreach ($key2[0] as $key2a) {
												$total2 = $total2 + $key2a;
											}
										}
									?>
									<label>{{number_format($total1+$total2,2,',','.')}} €</label>
								</td>
							</tr>
							<tr>
								<td>
									<label>Energía Activa</label>
								</td>
								<?php
									$i = 0;
									$index = 0;
									$aux_index = 'costeP';
								?>
								<td>
									@foreach($coste_activa as $coste)
										<?php
											$aux_coste_ac = 0;
										?>
										@foreach($coste[0] as $costea)
											<?php
												$aux_coste_ac += $costea;
											?>
										@endforeach
										C{{$i+1}}: {{number_format($aux_coste_ac,2,',','.')}} €<br>
										<?php array_push($array_Energia_Activa,$aux_coste_ac);  ?>
										<?php $i++; ?>
									@endforeach
								</td>
							</tr>
							<tr>
								<td>
									<label>Energía Reactiva</label>
								</td>
								<?php
									$i = 0;
									$index = 0;
									$aux_index = 'costeP';
								?>
								<td>
									@foreach($coste_reactiva as $coste)
										<?php
											$aux_coste_reac = 0;
										?>
										@foreach($coste[0] as $costea)
											<?php
												$aux_coste_reac += $costea;
											?>
										@endforeach
										C{{$i+1}}: {{number_format($aux_coste_reac,2,',','.')}} €<br>
										<?php array_push($array_Energia_Reactiva,$aux_coste_reac);  ?>
										<?php $i++; ?>
									@endforeach
								</td>
							</tr>
							<tr class="row-highlight">
								<td>
									<label>Término Potencia</label>
								</td>
								<td>
									<?php
										$total3 = 0;
										$index = 0;
										$aux_index = 'costeP';
										foreach ($potencia_contratada as $key3) {
											foreach ($key3[0] as $key3a) {
												$total3 = $total3 + $key3a;
											}
										}
									?>
									<?php
										$total8 = 0;
										foreach ($exceso_potencia as $key8) {
											foreach ($key8[0] as $key8a) {
												$total8 = $total8 + $key8a;
											}
										}
									?>
									<label>{{number_format(array_sum($total_termino_potencia),2,',','.')}} €</label><br>
								</td>
							</tr>
							<tr>
								<td>
									<label>Potencia Contratada</label>
								</td>
								<?php $i= 0; ?>
								<td>
									@foreach($potencia_contratada as $poten_contra)
										<?php
											$aux_poten_contra = 0;
										?>
										@foreach($poten_contra[0] as $poten_con)
											<?php
												$aux_poten_contra += $poten_con;
											?>
										@endforeach
										C{{$i+1}}: {{number_format($total_potencia_contratada[$i],2,',','.')}} €<br>
										<?php array_push($array_Potencia_Contratada,$aux_poten_contra);  ?>
										<?php $i++; ?>
									@endforeach
								</td>
							</tr>
							<tr>
								<td>
									<label>Excesos Potencia</label>
								</td>
								<?php $i= 0; ?>
								<td>
									@foreach($exceso_potencia as $exc_poten)
										<?php
											$aux_exc = 0;
										?>
										@foreach($exc_poten[0] as $exc_pot)
											<?php
												$aux_exc = $aux_exc + $exc_pot;
											?>
										@endforeach
										C{{$i+1}}: {{number_format($total_excesos_potencia[$i],2,',','.')}} €<br>
										<?php array_push($array_Exceso_Potencia,$aux_exc);  ?>
										<?php $i++; ?>
									@endforeach
								</td>
							</tr>
							<tr class="row-highlight">
								<td>
									<label>I.E.E.</label>
								</td>
								<?php
									$total4 = 0;
									foreach ($impuesto as $key4) {
										$total4 = $total4 + $key4;
									}
								?>
								<td>
									<label>{{number_format(array_sum($impuesto),2,',','.')}} €</label>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<?php
										$i = 0;
									?>
									@foreach($iee as $coste)
										<?php
											$aux_cost = 0;
											$k = 0;
											$j = 0;
										?>
										C{{$i+1}}: {{number_format($impuesto[$i],2,',','.')}} €<br>
										<?php array_push($array_IEE,$coste);  ?>
										<?php $i++; $j++;?>
									@endforeach
								</td>
							</tr>
							<tr class="row-highlight">
								<td>
									<label>Equipo de Medida</label>
								</td>
								<?php
									$total5 = 0;
									if(empty($equipo))
									{
										$total5 = 0;
									}else{
										if(!empty($equipo))
										{
											foreach ($equipo as $key5) {
												if(!empty($key5))
													$total5 = $total5 + ($key5[0]->valor*($diasDiferencia+1));
												else
													$total5 = $total5 + 0;
												}
										}else{
											$total5 = 0;
										}
									}
								?>
								<td>
									<label>{{number_format($total5,2,',','.')}} €</label>
								</td>
							</tr>
							<tr>
								<td></td>
								<?php $i= 0; ?>
								<td>
									@if(empty($equipo))
										C{{$i+1}}: 0 €<br>
									@else
										@foreach($equipo as $equi)
											@if(!empty($equi))
												C{{$i+1}}: {{number_format($equi[0]->valor*($diasDiferencia+1),2,',','.')}} €<br>
												<?php array_push($array_Equipo_Medida,$equi[0]->valor*($diasDiferencia+1));  ?>
											@else
												C{{$i+1}}: 0 €<br>
											@endif
											<?php $i++; ?>
										@endforeach
									@endif
								</td>
							</tr>
							<tr class="row-highlight">
								<td>
									<label>I.V.A. (21%)</label>
								</td>
								<td>
									<label>{{number_format(array_sum($IVA),2,',','.')}} €</label>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<?php $i= 0; ?>
									@foreach($IVA as $iva)
										<?php $i++; ?>
										@if(!empty($iva))
											C{{$i}}: {{number_format($iva,2,',','.')}} €<br>
											<?php array_push($array_IVA,$iva);  ?>
										@else
											C{{$i}}: 0 €<br>
										@endif
									@endforeach
								</td>
							</tr>
							<tr class="row-header">
								<th>TOTAL</th>
								<th>{{number_format($total1+$total2+array_sum($total_termino_potencia)+array_sum($impuesto)+$total5+array_sum($IVA),2,',','.')}} €</th>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="btn-container reverse">
					<button id="exportButton" class="btn">Generar PDF</button>											
				</div>
			</div>
		</div>
	</div>

	<div class="d-none">
		<table class="table table-bordered  table-hover table-condensed  tabla1 table-analisis-comparacion export-pdf table-responsive" data-pdforder="1"  id="tabla2" style="width: 70%; margin: 0px auto">
			<thead style="background-color: #004165;">
				<tr>
					<th class="text-center" style="color: #fff;">Concepto</th>
					<th class="text-center" style="color: #fff;">Cálculo</th>
					<th class="text-center" style="color: #fff;">Importe</th>
				</tr>
			</thead>
			<tbody>
				<tr class="text-left" style="background: #C1CCD3;">
					<td colspan="2">
						<label style="color: #004165;">Término Energía</label>
					</td>
					<td class="text-center">
						<?php
							$total1 = 0;
							$index = 0;
							foreach ($coste_activa as $key1) {
								$aux_index = 'costeP';
								foreach ($key1[0] as $key1a) {
									$total1 = $total1 + $key1a;
								}
							}
						?>
						<?php
						$total2 = 0;
						$index = 0;
						$aux_index = 'costeP';
						foreach ($coste_reactiva as $key2) {
							foreach ($key2[0] as $key2a) {
								$total2 = $total2 + $key2a;
							}
						}
						?>
						<label style="color: #004165;">{{number_format($total1+$total2,2,',','.')}} €</label>
					</td>
				</tr>
				<tr>
					<td>
						<label style="color: #004165; margin-left: 5%; font-weight: 100;">Energía Activa</label>
					</td>
					<?php
						$i = 0;
						$index = 0;
						$aux_index = 'costeP';
					?>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em; ">
						@foreach($coste_activa as $coste)
							<?php
								$aux_coste_ac = 0;
							?>
							@foreach($coste[0] as $costea)
								<?php
									$aux_coste_ac += $costea;
								?>
							@endforeach
							C{{$i+1}}: {{number_format($aux_coste_ac,2,',','.')}} €<br>
							<?php array_push($array_Energia_Activa,$aux_coste_ac);  ?>
							<?php $i++; ?>
						@endforeach
					</td>
				</tr>
				<tr>
					<td>
						<label style="color: #004165; margin-left: 5%; font-weight: 100;">Energía Reactiva</label>
					</td>
					<?php
						$i = 0;
						$index = 0;
						$aux_index = 'costeP';
					?>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						@foreach($coste_reactiva as $coste)
							<?php
								$aux_coste_reac = 0;
							?>
							@foreach($coste[0] as $costea)
								<?php
									$aux_coste_reac += $costea;
								?>
							@endforeach
							C{{$i+1}}: {{number_format($aux_coste_reac,2,',','.')}} €<br>
							<?php array_push($array_Energia_Reactiva,$aux_coste_reac);  ?>
							<?php $i++; ?>
						@endforeach
					</td>
				</tr>
				<tr class="text-left" style="background: #C1CCD3;">
					<td colspan="2">
						<label style="color: #004165;">Término Potencia</label>
					</td>
					<td class="text-center">
						<?php
							$total3 = 0;
							$index = 0;
							$aux_index = 'costeP';
							foreach ($potencia_contratada as $key3) {
								foreach ($key3[0] as $key3a) {
									$total3 = $total3 + $key3a;
								}
							}
						?>
						<?php
							$total8 = 0;
							foreach ($exceso_potencia as $key8) {
								foreach ($key8[0] as $key8a) {
									$total8 = $total8 + $key8a;
								}
							}
						?>
						<!-- <label style="color: #004165;">{{number_format($total3+$total8,2,',','.')}} €</label>  -->
						<label style="color: #004165;">{{number_format(array_sum($total_termino_potencia),2,',','.')}} €</label><br>
					</td>
				</tr>
				<tr>
					<td>
						<label style="color: #004165; margin-left: 5%; font-weight: 100;">Potencia Contratada</label>
					</td>
					<?php $i= 0; ?>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						@foreach($potencia_contratada as $poten_contra)
							<?php
								$aux_poten_contra = 0;
							?>
							@foreach($poten_contra[0] as $poten_con)
								<?php
									$aux_poten_contra += $poten_con;
								?>
							@endforeach
							<!--  C{{$i+1}}: {{number_format($aux_poten_contra,2,',','.')}} €<br>  -->
							C{{$i+1}}: {{number_format($total_potencia_contratada[$i],2,',','.')}} €<br>
							<?php array_push($array_Potencia_Contratada,$aux_poten_contra);  ?>
							<?php $i++; ?>
						@endforeach
					</td>
				</tr>
				<tr>
					<td>
						<label style="color: #004165; margin-left: 5%; font-weight: 100;">Excesos Potencia</label>
					</td>
					<?php $i= 0; ?>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						@foreach($exceso_potencia as $exc_poten)
							<?php
								$aux_exc = 0;
							?>
							@foreach($exc_poten[0] as $exc_pot)
								<?php
									$aux_exc = $aux_exc + $exc_pot;
								?>
							@endforeach
							{{-- C{{$i+1}}: {{number_format($aux_exc,2,',','.')}} €<br> --}}
							C{{$i+1}}: {{number_format($total_excesos_potencia[$i],2,',','.')}} €<br>
							<?php array_push($array_Exceso_Potencia,$aux_exc);  ?>
							<?php $i++; ?>
						@endforeach
					</td>
				</tr>
				<tr class="text-left" style="background: #C1CCD3;">
					<td colspan="2">
						<label style="color: #004165;">I.E.E.</label>
					</td>
					<?php
						$total4 = 0;
						foreach ($impuesto as $key4) {
							$total4 = $total4 + $key4;
						}
					?>
					<td class="text-center">
						{{-- <label style="color: #004165;">{{number_format($total4,2,',','.')}} €</label> --}}
						<label style="color: #004165;">{{number_format(array_sum($impuesto),2,',','.')}} €</label>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						<?php
							$i = 0;
						?>
						@foreach($iee as $coste)
							<?php
								$aux_cost = 0;
								$k = 0;
								$j = 0;
							?>
							C{{$i+1}}: {{number_format($impuesto[$i],2,',','.')}} €<br>
							<?php array_push($array_IEE,$coste);  ?>
							<?php $i++; $j++;?>
						@endforeach
					</td>
				</tr>
				<tr class="text-left" style="background: #C1CCD3;">
					<td colspan="2">
						<label style="color: #004165;">Equipo de Medida</label>
					</td>
					<?php
						$total5 = 0;
						if(empty($equipo))
						{
							$total5 = 0;
						}else{
							if(!empty($equipo))
							{
								foreach ($equipo as $key5) {
									if(!empty($key5))
										$total5 = $total5 + ($key5[0]->valor*($diasDiferencia+1));
									else
										$total5 = $total5 + 0;
									}
							}else{
								$total5 = 0;
							}
						}
					?>
					<td class="text-center">
						<label style="color: #004165;">{{number_format($total5,2,',','.')}} €</label>
					</td>
				</tr>
				<tr>
					<td></td>
					<?php $i= 0; ?>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						@if(empty($equipo))
							C{{$i+1}}: 0 €<br>
						@else
							@foreach($equipo as $equi)
								@if(!empty($equi))
									C{{$i+1}}: {{number_format($equi[0]->valor*($diasDiferencia+1),2,',','.')}} €<br>
									<?php array_push($array_Equipo_Medida,$equi[0]->valor*($diasDiferencia+1));  ?>
								@else
									C{{$i+1}}: 0 €<br>
								@endif
								<?php $i++; ?>
							@endforeach
						@endif
					</td>
				</tr>
				<tr class="text-left" style="background: #C1CCD3;">
					<td colspan="2">
						<label style="color: #004165;">I.V.A. (21%)</label>
					</td>
					<td class="text-center">
						<label style="color: #004165;">{{number_format(array_sum($IVA),2,',','.')}} €</label>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2" style="color: #004165; line-height: 200%; font-size: 0.8em;">
						<?php $i= 0; ?>
						@foreach($IVA as $iva)
							<?php $i++; ?>
							@if(!empty($iva))
								C{{$i}}: {{number_format($iva,2,',','.')}} €<br>
								<?php array_push($array_IVA,$iva);  ?>
							@else
								C{{$i}}: 0 €<br>
							@endif
						@endforeach
					</td>
				</tr>
				<tr style="background-color: #004165;">
					<th class="text-right" colspan="2" style="color: #fff;">TOTAL</th>
					<th class="text-center" style="color: #fff;">{{number_format($total1+$total2+array_sum($total_termino_potencia)+array_sum($impuesto)+$total5+array_sum($IVA),2,',','.')}} €</th>
				</tr>
			</tbody>
		</table>
	</div>

	<form method="post" class="d-none" id="form-pdf" action="{{route('exportacion.pdf',['titulo'=>$titulo,'date_from'=>$date_from,'date_to'=>$date_to,'contador_label'=>'Totalizador'])}}">
		{{ csrf_field() }}
	</form>

	<?php
		if (!isset($contador_count)) {
			$contador_count = 0;
		}

		$array_Pie_Coste_Total=array();
		for ($i = 0; $i < $contador_count ; $i++){
			array_push($array_Pie_Coste_Total,$array_Energia_Activa[$i]+$array_Energia_Reactiva[$i]+$total_termino_potencia[$i]+$impuesto[$i]+$array_Equipo_Medida[$i]+$IVA[$i]);
		}
		$array_Pie_Termino_Energia=array();
		for ($i = 0; $i < $contador_count ; $i++){
			array_push($array_Pie_Termino_Energia,$array_Energia_Activa[$i]+$array_Energia_Reactiva[$i]);
		}
		$array_Pie_Termino_Potencia=array();
		for ($i = 0; $i < $contador_count ; $i++){
			array_push($array_Pie_Termino_Potencia,$total_termino_potencia[$i]);
		}
	?>
@endsection

@section('modals')
	@include('Dashboard.modals.modal_intervals5')
@endsection

@section('scripts')
@include('Dashboard.includes.script_intervalos')
@include('Dashboard.includes.scripts_modal_interval')
<script src="{{asset('js/jquery.min.js')}}"> </script>
<script src="{{asset('js/canvas.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
<script>

	function changeFunc()
	{
		var selectBox = document.getElementById("option_interval");
		var selectedValue = selectBox.options[selectBox.selectedIndex].value;
		if(selectedValue == 9)
		{
			$('#div_datatimes').show();
			$('#datepicker').prop('required',true);
			$('#datepicker2').prop('required',true);
		}else{
			$('#div_datatimes').hide();
			$('#datepicker').val('');
			$('#datepicker2').val('');
			$('#datepicker').prop('required',false);
			$('#datepicker2').prop('required',false);
		}
	}
</script>
<script>
	$('#div_datatimes').hide();
	$('#datepicker').val('');
	$('#datepicker2').val('');
	$('#datepicker').prop('required',false);
	$('#datepicker2').prop('required',false);

	$( function() {
		$( "#datepicker" ).datepicker({
			dateFormat:'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
	} );
	$( function() {
		$( "#datepicker2" ).datepicker({
			dateFormat:'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});
	} );
</script>
<script>

	var Pie_Coste_Total = @php echo json_encode($array_Pie_Coste_Total); @endphp;
	var datapoins1 = [];
	var Pie_Termino_Energia = @php echo json_encode($array_Pie_Termino_Energia); @endphp;
	var datapoins2 = [];
	var Pie_Termino_Potencia = @php echo json_encode($array_Pie_Termino_Potencia); @endphp;
	var datapoins3 = [];

	for (var i = 0; i < Pie_Coste_Total.length; i++) {
		datapoins1.push({ label: "C"+[i+1], y: parseFloat(Pie_Coste_Total[i])});
	}
	for (var i = 0; i < Pie_Coste_Total.length; i++) {
		datapoins2.push({ label: "C"+[i+1], y: parseFloat(Pie_Termino_Energia[i])});
	}
	for (var i = 0; i < Pie_Coste_Total.length; i++) {
		datapoins3.push({ label: "C"+[i+1], y: parseFloat(Pie_Termino_Potencia[i])});
	}

	window.onload = function() {

		CanvasJS.addColorSet("blueShades",
				[//colorSet Array
					"#24437E",
					"#656565",
					"#426D9D",
					"#638CD4",
					"#B7B7B7",
					"#75ADE0",
					"#2F5AA6",
					"#4C78CA",
					"#507ACB",
					"#547DCB",
					"#A9A9A9"
				]);


		var chart1 = new CanvasJS.Chart("pie_total", {
			//	colorSet: "blueShades",
			height:350,
			animationEnabled: true,
			backgroundColor: "transparent",
			title: {
				text: "Coste Total",
				fontColor: "#004165",
				fontWeight: "bold",
				fontFamily: "Univers-45-Light",
				fontSize: "18"
			},
			data: [{
				type: "pie",
				radius: 100,
				startAngle: 270,
				toolTipContent: "{label} - #percent%",
				indexLabel: "{label} - #percent%",
				dataPoints: datapoins1

			}]
		});

		var chart2 = new CanvasJS.Chart("pie_energia", {
			//		colorSet: "blueShades",
			height:350,
			animationEnabled: true,
			backgroundColor: "transparent",
			title: {
				text: "Termino Energía",
				fontColor: "#004165",
				fontWeight: "bold",
				fontFamily: "Univers-45-Light",
				fontSize: "18"
			},
			data: [{
				type: "pie",
				radius: 100,
				startAngle: 270,
				toolTipContent: "{label} - #percent%",
				indexLabel: "{label} - #percent%",
				dataPoints: datapoins2

			}]
		});

		var chart3 = new CanvasJS.Chart("pie_potencia", {
			//		colorSet: "blueShades",
			height:350,
			animationEnabled: true,
			backgroundColor: "transparent",
			title: {
				text: "Termino Potencia",
				fontColor: "#004165",
				fontWeight: "bold",
				fontFamily: "Univers-45-Light",
				fontSize: "18"
			},
			data: [{
				type: "pie",
				radius: 100,
				startAngle: 270,
				toolTipContent: "{label} - #percent%",
				indexLabel: "{label} - #percent%",
				dataPoints: datapoins3

			}]
		});

		chart1.render();
		chart2.render();
		chart3.render();

	}


	$("#exportButton").click(function(){
		var idxBreak = "";
		var tokenInput = $("#form-pdf input[name='_token']")[0].outerHTML;
		$("#form-pdf").html("");
		$("#form-pdf").append(tokenInput);

		var header = $(".pdf-header")[0].outerHTML;

		var input = $("<input name='elements[]' type='hidden' value='"+btoa(unescape(encodeURIComponent(header)))+"' />");
		var type = $("<input name='type_elements[]' value='2' type='hidden' />");
		$("#form-pdf").append(input);
		$("#form-pdf").append(type);

		var objActive = $(".active.plot-tab .graph-1");
		var width = parseInt(objActive.width());
		var height = 350;

		var cntChart = $(".plot-tab");
		var handleCharts = [];
		var dataCharts = [];

		var idxElement = 1;

		var cnv1 = $("#pie_total .canvasjs-chart-canvas").get(0);
		var cnv2 = $("#pie_energia .canvasjs-chart-canvas").get(0);
		var cnv3 = $("#pie_potencia .canvasjs-chart-canvas").get(0);


		var canvas = document.createElement('canvas'),
				ctx = canvas.getContext('2d'),
				width = cnv1.width + cnv2.width + cnv3.width,
				height = cnv1.height;

		canvas.width = width;
		canvas.height = 350;
		[{
			cnv: cnv1,
			x: 0
		},
			{
				cnv: cnv2,
				x: cnv1.width
			},
			{
				cnv: cnv3,
				x: cnv1.width + cnv2.width
			}].forEach(function(n) {
			ctx.beginPath();
			ctx.drawImage(n.cnv, n.x, 0, n.cnv.width, height);
		});

		var data = canvas.toDataURL('image/png', 1.0);
		dataCharts.push(data);

		for(var i = 0; i < dataCharts.length; i++) {
			var input = $("<input name='elements[]' type='hidden' value='"+dataCharts[i]+"' />");
			var type = $("<input name='type_elements[]' value='1' type='hidden' />");
			$("#form-pdf").append(input);
			$("#form-pdf").append(type);

			if(idxBreak.indexOf("," + idxElement + ",") > 0) {
				var input = $("<input name='elements[]' type='hidden' value='break' />");
				var type = $("<input name='type_elements[]' value='3' type='hidden' />");
				$("#form-pdf").append(input);
				$("#form-pdf").append(type);
			}
			idxElement++;

		}


		var htmlData = $(".export-pdf");
		/*donde pone el uno i < 1;  <--- es el numero de ".export-pdf que hay htmlData.length no funcciona correctamente por eso lo he puesto manual*/
		var arrIdx = [];
		for(var i = 0; i < htmlData.length; i++) {
			var idxPdf = $(htmlData[i]).data("pdforder");
			arrIdx.push([parseInt(idxPdf), i]);
		}


		arrIdx.sort(function(left, right) {
			return left[0] < right[0] ? -1 : 1;
		});

		for(var i = 0; i < arrIdx.length; i++) {
			var idx = arrIdx[i][1];
			var input = $("<input name='elements[]' type='hidden' value='"+btoa(unescape(encodeURIComponent(htmlData[idx].outerHTML)))+"' />");
			var type = $("<input name='type_elements[]' value='2' type='hidden' />");
			$("#form-pdf").append(input);
			$("#form-pdf").append(type);

			var idxPDF = arrIdx[i][0];
			if(idxBreak.indexOf("," + idxPDF + ",") >= 0) {
				var input = $("<input name='elements[]' type='hidden' value='break' />");
				var type = $("<input name='type_elements[]' value='3' type='hidden' />");
				$("#form-pdf").append(input);
				$("#form-pdf").append(type);

			}
		}

		$("#form-pdf").submit();
		return false;
	});

</script>
<script>
	function initialize(){
		const mapContainer = document.querySelector("#map")
		const mapOptions = {
			mapTypeId: 'roadmap',
			gestureHandling: 'greedy'
		}
		const map = new google.maps.Map(mapContainer, mapOptions)
		const mapBounds = new google.maps.LatLngBounds()
		const markers = JSON.parse(`@php echo $markers; @endphp`)

		setMarkers(map, mapBounds, markers)
		map.setTilt(45)
		map.fitBounds(mapBounds)
	}

	function setMarkers(map, mapBounds, markers){
		let lastInfoWindow = null
		
		markers.forEach((marker) => {
			const position = new google.maps.LatLng(marker.lat, marker.lng)
			const mapMarker = new google.maps.Marker({
				position: position,
				map,
				// title: marker.name,
				label: marker.custom_label
			})
			const infoWindow = createInfoWindow(marker)
			const infoWindowHandler = () => {
				if(lastInfoWindow) lastInfoWindow.close()
				infoWindow.open(map, mapMarker)
				lastInfoWindow = infoWindow
			}

			mapBounds.extend(position)
			mapMarker.addListener("click", infoWindowHandler)
			mapMarker.addListener("mouseover", infoWindowHandler)
		})
	}

	function createInfoWindow(marker){
		const infoWindow = document.createElement('div')
		const strong = document.createElement('strong')
		const text = document.createElement('text')

		strong.innerText = marker.name
		infoWindow.appendChild(strong)
		infoWindow.appendChild(document.createElement('br'))
		text.innerText = marker.address
		infoWindow.appendChild(text)

		return new google.maps.InfoWindow({
			content: infoWindow
		})
	}
</script>
<script async defer src='{{$maps_url}}'></script>
@endsection
