<!DOCTYPE HTML>
<html>
    <head>
        <title>Submeter 4.0 | Home</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <link href="{{asset('css/bootstrap.min.css')}}" rel='stylesheet' type='text/css' />
        <script src="{{asset('js/Chart.js')}}"></script>
    </head>
    <body>
    	<header class="clearfix">
	      <div class="row">
	      	<div class="col-md-3">
	      		<div id="logo">
			        <img src="{{asset($image)}}" style="width: 80px; height: 80px">			        
			    </div>
	      	</div>
	      	<div class="col-md-9">
		      <div id="company">
		        <h2 class="name">Empresa: {{$user->name}}</h2>
		        @if(!is_null($user->_perfil))
		        <div>Dirección: {{$user->_perfil->direccion}}</div>        
		        @else
		        	<div>Dirección: Sin dirección</div>
		        @endif
		        <div>Contador: {{$array_contadores}}</div>
		        <div>Email: {{$user->email}}</div>
		        <div>
		        	Intervalo del reporte: Desde {{$date_from}} hasta {{$date_to}}
		        </div>
		      </div>
	      	</div>
	      </div>
	    </header>
		<br>
		<main>
			<div class="col-md-12">
				<?php $j = 1; ?>
				@foreach($user->_count as $_contador)
					<table class="table table-hover table-responsive table-bordered" style="margin: 0px auto;">
						<thead>
							<tr>
								<th style="text-align: center; font-size: 12pt; background-color: #004165;
	  color: white; width: 200px">
									Concepto
								</th>
								<th style="text-align: center; font-size: 12pt; background-color: #004165;
	  color: white;">
									Cálculo
								</th>
								<th style="text-align: center; font-size: 12pt; background-color: #004165; width: 110px;
	  color: white;">
									Importe
								</th>
							</tr>
						</thead>
						<tbody>
							<tr class="text-left">
								<td>
									<label style="color: #004165;">Término Energía</label>
								</td>
								<td></td>															
								<td class="text-center" style="color: #004165;">
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
									 <b>{{number_format($total1+$total2,2,',','.')}} €</b>
								</td>
							</tr>
							<tr class="text-left">
								<td>
									<label style="color: #004165; margin-left: 10%; font-weight: 100;">Energía Activa</label>
								</td>
								<?php 
									$i = 0; 
									$index = 0;
									$aux_index = 'costeP';
								?>
								<td style="color: #004165;">
									@foreach($coste_activa as $coste)
										<?php 
											$aux_coste_ac = 0;
										 ?>
										@foreach($coste[0] as $costea)
											<?php 
												$aux_coste_ac += $costea;
											 ?>
										@endforeach
										C{{$i+1}}: {{number_format($aux_coste_ac,2,',','.')}} €</i><br>
									<?php $i++; ?>
									@endforeach
								</td>
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
								<td class="text-center">
									
								</td>
							</tr>
							
							<tr class="text-left">
								<td>
									<label style="color: #004165; margin-left: 10%; font-weight: 100;">Energía Reactiva</label>
								</td>
								<td style="color: #004165;">
									<?php $i = 0; ?>
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
									<?php $i++; ?>
									@endforeach
								</td>
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
								<td class="text-center">
									
								</td>
							</tr>
							<tr class="text-left">
								<td>
									<label style="color: #004165;">Término Potencia</label>
								</td>
								<td></td>															
								<td class="text-center" style="color: #004165">
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
									 <b>{{number_format($total3+$total8,2,',','.')}} €</b>
								</td>
							</tr>
							<tr class="text-left">
								<td>
									<label style="color: #004165; margin-left: 10%; font-weight: 100;">Potencia Contratada</label>
								</td>
								<?php $i= 0; ?>
								<td style="color: #004165;">
									@foreach($potencia_contratada as $poten_contra)
										<?php 
											$aux_poten_contra = 0;
										 ?>
										@foreach($poten_contra[0] as $poten_con)
											<?php 
												$aux_poten_contra += $poten_con;
											 ?>
										@endforeach
										C{{$i+1}}: {{number_format($aux_poten_contra,2,',','.')}} €<br>
										<?php $i++; ?>
									@endforeach
								</td>								
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
								<td class="text-center">
									
								</td>
							</tr>
							
							<tr class="text-left">
								<td>
									<label style="color: #004165; margin-left: 10%; font-weight: 100;">Excesos Potencia</label>
								</td>
								<?php $i= 0; ?>
								<td style="color: #004165;">
									@foreach($exceso_potencia as $exc_poten)
										<?php 
											$aux_exc = 0;
										 ?>
										@foreach($exc_poten[0] as $exc_pot)
											<?php 
												$aux_exc = $aux_exc + $exc_pot;
											 ?>
										@endforeach
										C{{$i+1}}: {{number_format($aux_exc,2,',','.')}} €<br>
										<?php $i++; ?>
									@endforeach
								</td>
								<?php 
									$total8 = 0;
									foreach ($exceso_potencia as $key8) {
										foreach ($key8[0] as $key8a) {
											$total8 = $total8 + $key8a;
										}
									}
								 ?>
								<td class="text-center">
									
								</td>
							</tr>
							
							<tr >
								<td class="text-left">
									<label style="color: #004165;">I.E.E.</label>
								</td>
								<td style="color: #004165;">														
									<?php 
										$i = 0;
									?>
									@foreach($iee as $coste)
										<?php 
											$aux_cost = 0;
											$k = 0;
										 ?>
										
										C{{$i+1}}: {{number_format($coste,2,',','.')}} €<br>
										<?php $i++; ?>
									@endforeach
								</td>
								<?php 
									$total4 = 0;
									foreach ($impuesto as $key4) {
										$total4 = $total4 + $key4;
									}
								 ?>
								<td class ="text-center" style="color: #004165">
									<b>{{number_format($total4,2,',','.')}} €</b>
								</td>
							</tr>
							
							<tr class="text-left">
								<td>
									<label style="color: #004165;">Equipo de Medida</label>
								</td>
								<?php $i= 0; ?>
								<td style="color: #004165;">
									@if(empty($equipo))
												C{{$i+1}}: 0 €<br>
									@else
										@foreach($equipo as $equi)
											@if(!empty($equi))
												C{{$i+1}}: {{number_format($equi[0]->valor*($diasDiferencia+1),2,',','.')}} €<br>
											 @else
											 	C{{$i+1}}: 0 €</i><br>
											 @endif
											<?php $i++; ?>
										@endforeach
									@endif
								</td>
								<?php 
									$total5 = 0;
									if(empty($equipo))
									{
										$total5 = 0;
									}else
									{
										if(!empty($equipo))
										{
											foreach ($equipo as $key5) {
												if(!empty($key5))
													$total5 = $total5 + $key5[0]->valor*($diasDiferencia+1);
												else
													$total5 = $total5 + 0;
											}
										}else{
											$total5 = 0;
										}
									}

								 ?>
								<td class="text-center" style="color: #004165">
									<b>{{number_format($total5,2,',','.')}} €</b>
								</td>
							</tr>
							
							<tr class="text-left">
								<td>
									<label style="color: #004165;">I.V.A.</label>
								</td>
								<td></td>
								<td class="text-center" style="color: #004165">
									<b>{{(number_format((($total1+$total2+$total3+$total4+$total5+$total8)*.21),2,',','.'))}} €</b>
								</td>
							</tr>
							<tr>
								<td></td>
								<td style="color: #004165;">														
									<?php $i = 0; ?>
									@if(empty($equipo))
										@foreach($coste_activa as $coste)
											<?php 
												$aux_cost = 0;
												$k = 0;
											 ?>
											@foreach($coste as $cost)
												<?php 
													$aux_cost += ($cost+$coste_reactiva[$i][0][$aux_index.($k+1)]+$potencia_contratada[$i][0][$aux_index.($k+1)]+$exceso_potencia[$i][0][$aux_index.($k+1)]+$iee[$i])*0.21;
													$k++;
												 ?>
											@endforeach
											C{{$i+1}}: {{number_format($aux_cost,2,',','.')}} €<br>
											<?php $i++; ?>
										@endforeach
									@else
										@foreach($equipo as $equi)
											@if(!empty($equi))
												<?php 
													$aux_cost = 0;
													$k = 0;
												 ?>
												@foreach($coste_activa[$i][0] as $cost)
													<?php 
														$aux_cost += ($cost+$coste_reactiva[$i][0][$aux_index.($k+1)]+$potencia_contratada[$i][0][$aux_index.($k+1)]+$exceso_potencia[$i][0][$aux_index.($k+1)]+$iee[$i])*0.21;
														$k++;
													 ?>
												@endforeach
												C{{$i+1}}: {{number_format($aux_cost+(($equi[0]->valor*($diasDiferencia+1))*.21),2,',','.')}} €<br>		
											 @else
											 	<?php 
													$aux_cost = 0;
													$k = 0;
												 ?>
												@foreach($coste_activa[$i][0] as $cost)
													<?php 
														$aux_cost += ($cost+$coste_reactiva[$i][0][$aux_index.($k+1)]+$potencia_contratada[$i][0][$aux_index.($k+1)]+$exceso_potencia[$i][0][$aux_index.($k+1)]+$iee[$i])*0.21;
														$k++;
													 ?>
												@endforeach
											 	C{{$i+1}}: {{number_format($aux_cost,2,',','.')}} €<br>
											 @endif
											<?php $i++; ?>
										@endforeach
									@endif
								</td>																
								<td></td>
							</tr>							
						</tbody>
						<tfoot style="background-color: #004165;">										
							<tr>
								<th></th>
								<th class="text-right" style="color: #fff;">TOTAL</th>
								<th class="text-center" style="color: #fff;">{{number_format($total1+$total2+$total3+$total4+$total5+$total8+($total1+$total2+$total3+$total5+$total4+$total8)*.21,0,',','.')}} €</i></th>	
							</tr>
						</tfoot>
					</table>
					<?php $j++; ?>
					@break
				@endforeach
			</div>
		</main>
    </body>
</html>
	