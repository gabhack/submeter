@extends('Dashboard.layouts.global5')

@section('intervals')
	@include('Dashboard.includes.intervalos')
@endsection

@section('counters')
	@include('Dashboard.includes.contadores', ["menuId" => 1])
@endsection

@section('content')

	@if (Session::has('message-error'))
		<div id="message-danger" class="alert alert-danger">{{ Session::get('message-error') }}</div>
	@endif

	{{-- <div id="myTabContent"> --}}
		{{-- <div role="tabpanel" id="Contador0" aria-labelledby="Contador0" class="graph-div1"> --}}
	{{-- <div class="graph-div1" style="margin-block: 1rem;"> --}}
	{{-- <div class="graph-div1"> --}}
		@php
			$aux_cont = implode('_', explode(' ', $contador_label))
		@endphp
<div class="row row-2 row-lg">
	<div class="column">
		<div class="graph shadow">
			@if($tipo_count == 1)
				@include('Dashboard.Graficas.potencia_demanda_control_optima',array('id_var' => 'PotenciaDemanOptima_'.$aux_cont, 'EAct' => $EAct, 'p_contratada' => $p_contratada, 'periodos2' => $periodos2))
			@else
				@include('Dashboard.Graficas.consumida_activa_reactiva',array('id_var' => 'ConsumidaActReac_'.$aux_cont, 'Energia_Act' => $Energia_Act, 'Energia_Reac_Cap' => $Energia_Reac_Cap, 'Energia_Reac_Induc' => $Energia_Reac_Induc, 'periodos2' => $periodos2))
			@endif
		</div>
	</div>
	<div class="column">
		<div class="graph shadow">
			@if($tipo_count == 1)
				@include('Dashboard.Graficas.coste_termino_potencia',array('id_var' => 'CostePotencia_'.$aux_cont, 'coste_potencia' => $coste_potencia, 'periodos_coste' => $periodos_coste))
			@else
				@include('Dashboard.Graficas.coste_termino_energia',array('coste' => $coste_termino_energia, 'periodos_coste' => $periodos_coste, 'id_var' => 'CosteTerminoEnergia_'.$aux_cont))
			@endif
		</div>
	</div>
</div>
<div class="row row-2 row-lg">
	<div class="column">
		<div class="graph shadow">										
			@if ($tipo_count == 1)
				@include('Dashboard.Graficas.consumida_activa_reactiva',array('id_var' => 'ConsumidaActReac_'.$aux_cont, 'Energia_Act' => $Energia_Act, 'Energia_Reac_Cap' => $Energia_Reac_Cap, 'Energia_Reac_Induc' => $Energia_Reac_Induc, 'periodos2' => $periodos2))
			@elseif ($tipo_count == 2 && $subtipo_count == 0)
				@include('Dashboard.Graficas.analisis_potencia',array('id_var' => 'Generacion_'.$aux_cont))
			@elseif($tipo_count == 2 && $subtipo_count == 1)
				@include('Dashboard.Graficas.consumida_activa_reactiva',array('id_var' => 'GeneracionType2_'.$aux_cont, 'Energia_Act' => $Energia_Act, 'Energia_Reac_Cap' => $Energia_Reac_Cap, 'Energia_Reac_Induc' => $Energia_Reac_Induc, 'periodos2' => $periodos2))
			@else  
				@include('Dashboard.Graficas.analisis_potencia',array('id_var' => 'Generacion_'.$aux_cont))
			@endif
		</div>
	</div>
	<div class="column">
		<div class="graph shadow">											
			@if ($tipo_count == 1)
				@include('Dashboard.Graficas.coste_termino_energia',array('coste' => $coste_termino_energia, 'periodos_coste' => $periodos_coste, 'id_var' => 'CosteTerminoEnergia_'.$aux_cont))
			@elseif ($tipo_count == 2 && $subtipo_count == 0)
				@include('Dashboard.Graficas.coste_termino_energia',array('coste' => $coste_termino_energia, 'periodos_coste' => $periodos_coste, 'id_var' => 'VentasEnergia_'.$aux_cont))
			@elseif($tipo_count == 2 && $subtipo_count == 1)
				@include('Dashboard.Graficas.coste_termino_energia',array('coste' => $coste_termino_energia, 'periodos_coste' => $periodos_coste, 'id_var' => 'VentasEnergiaType2_'.$aux_cont))
			@else  
				@include('Dashboard.Graficas.coste_termino_energia',array('coste' => $coste_termino_energia, 'periodos_coste' => $periodos_coste, 'id_var' => 'VentasEnergia_'.$aux_cont))
			@endif											
		</div>
	</div>
</div>
<div class="row">
	<div class="column">
		<div class="graph shadow">
			@if($tipo_count == 1)
				<div id="plotConsumo" class="grid-2 plot-cnt">
					<input type="hidden" class="plot-labels" value='{!! $dataPlotting["consumo"]["labels"] !!}' />
					<input type="hidden" class="plot-time-label" value='{{ $dataPlotting["consumo"]["time_label"] }}' />
					<input type="hidden" class="plot-max" value='{{ $dataPlotting["consumo"]["max"] }}' />
					@foreach($dataPlotting["consumo"]["series"] as $serie)
						<input type="hidden" class="serie-value" value='{!! $serie["values"] !!}' />
					@endforeach
					<div id="consumo_energia" style="height: 330px; width: 100%;"></div>
				</div>
			@else
				<div id="plotBalance" class="grid-2 plot-cnt">
					<input type="hidden" class="plot-labels" value='{!! $dataPlotting["balance"]["labels"] !!}' />
					<input type="hidden" class="plot-time-label" value='{{ $dataPlotting["balance"]["time_label"] }}' />
					<input type="hidden" class="plot-max" value='{{ $dataPlotting["balance"]["max"] }}' />
					@foreach($dataPlotting["balance"]["series"] as $serie)
						<input type="hidden" class="serie-value" value='{!! $serie["values"] !!}' />
					@endforeach
					<div id="balance_energia" style="height: 330px; width: 100%;"></div>
				</div>
			@endif
		</div>
	</div>
</div>
	{{-- </div> --}}
@endsection

@section('modals')
		@include('Dashboard.modals.modal_intervals5')
@endsection

@section('scripts')
	@include('Dashboard.includes.scripts_modal_interval')
	@include('Dashboard.includes.script_intervalos')

	<script src="{{asset('js/jquery.metisMenu.js')}}"></script>
	<script src="{{asset('js/custom.js')}}"></script>
	<script src="{{asset('js/screenfull.js')}}"></script>
	<script src="{{asset('js/scripts.js')}}"></script>
	{{-- <script src="{{asset('js/bootstrap.min.js')}}"> </script> --}}
	<script src="{{asset('js/canvas.js')}}"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	@include('Dashboard.includes.scripts_graficas')
	<script type="text/javascript">
		function togglePlotsDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
				e.dataSeries.visible = false;
			} else {
				e.dataSeries.visible = true;
			}
			var cnt = $(e.chart.container).closest(".plot-cnt");
			if(cnt.length > 0) {
				chart = cnt.data("chart");
				if(chart != undefined) {
					chart.render();
				}
			}
		}

		function clickDataSeries(e){
				var idx = e.dataPointIndex;
			var datesBegin = $("#form-subperiod [name='dates_begin']");
			var datesEnd = $("#form-subperiod [name='dates_end']");
			if(datesBegin.length > 0 && datesEnd.length > 0) {
				datesBegin = datesBegin.val();
				datesBegin = $.parseJSON(datesBegin);
				datesEnd = datesEnd.val();
				datesEnd = $.parseJSON(datesEnd);

				if(datesBegin !== undefined && datesEnd != undefined){
					if(datesBegin.length > 0 && datesEnd.length > 0 && datesBegin.length > idx && datesEnd.length > idx){
						$("#form-subperiod [name='date_from_personalice']").val(datesBegin[idx]);
						$("#form-subperiod [name='date_to_personalice']").val(datesEnd[idx]);
						$("#form-subperiod").submit();
					}
				}
			}
			return false;
		}

	function renderConsumoPlot() {
		var cnt = $("#consumo_energia");
		if(cnt.length == 0) {
			return false;
		}

		var cntData = $("#plotConsumo");
		var dataLabels = cntData.find(".plot-labels").val();
		dataLabels = $.parseJSON(dataLabels);
		var aux_interval = cntData.find(".plot-time-label").val();
		var maxData = cntData.find(".plot-max").val();

		var axisYData = {
					suffix: " kWh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
		};
		var axisY2Data = {
					suffix: " kVArh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
			};

		if($.isNumeric(maxData)) {
			maxData = parseFloat(maxData);
			maxData = 1.1 * maxData;
			axisYData.maximum = maxData;
			axisY2Data.maximum = maxData;
		}


		var seriesValues = cntData.find(".serie-value");
		var data = new Array();

		var colors = ['#004165', '#B9C9D0', '#7D9AAA'];

		for(var i = 0; i <  seriesValues.length; i++) {
			var serieVal = $(seriesValues[i]).val();
			serieVal = $.parseJSON(serieVal);
			var seriedata = new Array();
			for(j = 0; j < dataLabels.length; j++) {
				var d = {
						y : serieVal[j],
						x : j,
						label: dataLabels[j],
						color: colors[i],
							click: clickDataSeries
				};
				seriedata.push(d);
			}
			data[i] = seriedata;
		}

		CanvasJS.addCultureInfo("es", {
				decimalSeparator: ",",
					digitGroupSeparator: "."
			});
					var titulo = "{{$titulo}}";  
					var conta = "{{ $contador2->count_label }}";
					var date_to = "{{ $date_to }}";
					var date_from ="{{ $date_from }}";
		var chart5 = new CanvasJS.Chart("consumo_energia", {

				theme: "light2",
				culture: "es",
				title:{
					text: "Consumo Diario Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
							exportFileName: "ConsumoDiarioEnergía-"+conta+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisX: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165",
					interval: 1
				},
				axisY: axisYData,
				axisY2: axisY2Data,
				toolTip: {
					shared: true
				},
				legend: {
					cursor:"pointer",
					itemclick: togglePlotsDataSeries
				},
				data: [{
					type: "column",
					cursor: "zoom-in",
					bevelEnabled: true,
					name: "Energía Activa",//Label del cursor
					legendText: "Energía Activa", // Label del legend
					legendMarkerColor: "#004165",
					toolTipContent: aux_label+"{label} <br> {name}: {y} kWh",
					showInLegend: true,
					dataPoints: data[0]
				},
				{
					type: "column",
					name: "Energía Reactiva Ind", // Label del cursor
					legendText: "Energía Reactiva Ind", // Label del legend
					legendMarkerColor: "#B9C9D0",
					toolTipContent: "{name}: {y} kVArh",
					bevelEnabled: true,
					axisYType: "secondary",
					showInLegend: true,
					dataPoints:data[1]
				},
				{
					type: "column",
					name: "Energía Reactiva Cap", // Label del cursor
					legendText: "Energía Reactiva Cap", // Label del legend
					toolTipContent: "{name}: {y} kVArh",
					bevelEnabled: true,
					legendMarkerColor: "#7D9AAA",
					axisYType: "secondary",
					showInLegend: true,
					dataPoints:data[2]
				}]
			});
		chart5.render();
		cntData.data("chart", chart5);
	}

		function renderBalancePlot() {
			var cnt = $("#balance_energia");
			if(cnt.length == 0) {
				return false;
			}

			var cntData = $("#plotBalance");
			var dataLabels = cntData.find(".plot-labels").val();
			dataLabels = $.parseJSON(dataLabels);
			var aux_interval = cntData.find(".plot-time-label").val();
			var maxData = cntData.find(".plot-max").val();
			if($.isNumeric(maxData)) {
			maxData = parseFloat(maxData);
			maxData = 1.1 * maxData;
		}

			var seriesValues = cntData.find(".serie-value");
			var data = new Array();

			var colors = ['#004165', '#B9C9D0', '#7D9AAA'];

			for(var i = 0; i <  seriesValues.length; i++) {
				var serieVal = $(seriesValues[i]).val();
				serieVal = $.parseJSON(serieVal);
				var seriedata = new Array();
				for(j = 0; j < dataLabels.length; j++) {
					var d = {
							y : serieVal[j],
							x : j,
							label: dataLabels[j],
						color: colors[i],
							click: clickDataSeries
					};
					seriedata.push(d);
				}
				data[i] = seriedata;
			}

			CanvasJS.addCultureInfo("es", {
					decimalSeparator: ",",
						digitGroupSeparator: "."
				});

					var titulo = "{{$titulo}}";  
					var conta = "{{ $contador2->count_label }}";
					var date_to = "{{ $date_to }}";
					var date_from ="{{ $date_from }}";
			var chart = new CanvasJS.Chart("balance_energia", {
			theme: "light2",
			culture: "es",
			title:{
				text: "Consumo Neto Energía",
				fontSize: 18,
				margin: 50,
				fontColor: "#004165"
			},
							exportFileName: "ConsumoNetoEnergía-"+conta+"-"+date_from+"-"+date_to,
			exportEnabled: true,
			axisY: {
				suffix: " kWh",
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165"
			},
			axisX: {
				titleFontColor: "#004165",
				titleFontSize: 12,
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165",
				interval: 1
			},
			toolTip: {
				shared: "true"
			},
			legend:{
				cursor:"pointer",
				itemclick : togglePlotsDataSeries
			},
			data: [{
				type: "spline",
				cursor: "zoom-in",
				visible: true,
				showInLegend: true,
				toolTipContent: aux_label+"{label} </br> {name}: {y} kWh",
				name: "Consumo de Energía",
				legendMarkerColor: "#004165",
				dataPoints: data[0]
			},
			{
				type: "spline",
				cursor: "zoom-in",
				visible: true,
				showInLegend: true,
				toolTipContent: "{name}: {y} kWh",
				name: "Generación de Energía",
				legendMarkerColor: "#B9C9D0",
				dataPoints: data[1]
			},
			{
				type: "spline",
				cursor: "zoom-in",
				visible: true,
				showInLegend: true,
				toolTipContent: "{name}: {y} kWh",
				name: "Balance Neto",
				legendMarkerColor: "#004165",
				dataPoints: data[2]
			},
			]
		});
			chart.render();
			cntData.data("chart", chart);
		}

		$(document).ready(function(){
			renderConsumoPlot();
			renderBalancePlot();
		});
</script>
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
	function anterior()
	{
		$('#before_navigation').val("-1");
	}
	function siguiente()
	{
		$('#before_navigation').val("1");
	}
	function volver()
	{
		$('#before_navigation').val("0");
	}
</script>
{{-- <script src="{{asset('js/pie-chart.js')}}" type="text/javascript"></script> --}}
<script src="{{asset('js/skycons.js')}}"></script>
@endsection