@extends('Dashboard.layouts.global5')

@section('intervals')
<div class="content intervals">
	<div class="intervalo-izquierda">
		<button type="button" class="btn active btn-middle btn-primary-submeter" data-toggle="modal" data-target="#myModal">
				<i class="fa fa-plus"></i>  <div class="d-sm-inline">Intervalos de Períodos ({{$label_intervalo}})</div> <div class="d-none d-sm-none">{{$label_intervalo}}</div>
		</button>
	</div>
	<div class="intervalo-derecha col-8 col-lg-6 text-center text-lg-right mt-1 mt-sm-3 mt-lg-1 pr-lg-5 font-nav-buttons">
		<form id="form_navigation" action="{{route('config.navigation')}}" method="POST">
			{{ csrf_field() }}
			<input type="hidden" name="option_interval" value="9">
			<input type="hidden" name="label_intervalo" value="{{$label_intervalo}}">
			<input type="hidden" name="date_from_personalice" value="{{$date_from}}">
			<input type="hidden" name="date_to_personalice" value="{{$date_to}}">
			<input type="hidden" name="before_navigation" id="before_navigation" value="0">
			<button type="submit" class="btn active btn-primary-submeter btn-arrow-left" onclick="anterior()">
				<i class="fas fa-angle-double-left"></i>
			</button>
			@if(isset(Session::get('_flash')['current_date']))
					<button type="button" class="btn active btn-link btn-auto-responsive"><i class="fas fa-pencil-alt" style="background-color: #286090; padding: 0.2rem; border-radius: 0.3rem; margin-right: 0.3rem;" aria-hidden="true"></i> {{Session::get('_flash')['current_date']}}</button>
			@else
					<button type="button" class="btn active btn-link btn-auto-responsive"><i class="fas fa-pencil-alt" style="background-color: #286090; padding: 0.2rem; border-radius: 0.3rem; margin-right: 0.3rem;" aria-hidden="true"></i> {{$label_intervalo}} </button>
			@endif
			<button type="submit" class="btn active btn-primary-submeter btn-arrow-right" onclick="siguiente()">
				<i class="fas fa-angle-double-right"></i>
			</button>
		</form>
	</div>
	@if(isset($ctrl) && $ctrl == 1)
		<a href="{!! route('admin.users',[2, $id]) !!}" class="btn btn-info btn-lg"><i class="fa fa-undo"></i></a>
	@endif
</div>
@endsection

@section('counters')
<div class="content counters">
	<a class="btn btn-sm float-right float-right-custom float-right-custom-1 btn-outline-primary" href="{{route('statistics.list',['type'=>$type,'user_id'=>$user->id])}}" class=" hvr-bounce-to-right">
		<i class="fa fa-cogs"></i><span>Editar configuración</span>
	</a>
	@if($type == 'indicadores')
		<a class="btn btn-sm float-right float-right-custom float-right-custom-2 btn-outline-secondary mr-2" href="{{route('statistics.manual_data',['user_id'=>$user->id])}}" class=" hvr-bounce-to-right">
			<i class="fa fa-database"></i><span>Datos manuales</span>
		</a>
	@endif
	<ul class="btn-list nav-header nav-pills contador-pill nav-pills-inline">
	@foreach($user->energy_meters as $i => $contador)
		@if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, $type === 'indicadores' ? '16' : '15', $contador->id))
			@if($contador->id == $user->current_count->meter_id)
				<li class="btn active nav-item">
					<a class="nav-link bg-submeter-3 active text-white" href="{{route('energymeter.change', ['user_id'=>$user->id, 'meter_id'=>$contador->id])}}">
						<i class="fa fa-clock-o"></i>  <span>{{$contador->count_label}}</span>
					</a>
				</li>
			@else
				<li class="btn nav-item">
					<a class="nav-link bg-submeter-5" href="{{route('energymeter.change', ['user_id'=>$user->id, 'meter_id'=>$contador->id])}}">
						<i class="fa fa-clock-o"></i>  <span>{{$contador->count_label}}</span>
					</a>
				</li>
			@endif
		@endif
	@endforeach
	</ul>
	<div class="dropdown">
	@foreach($user->energy_meters as $i => $contador)
		@if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, $type === 'indicadores' ? '16' : '15', $contador->id))
			@if($contador->id == $user->current_count->meter_id)
			<button type="button" class="btn active dropdown__button">
				<i class="fa fa-clock-o"></i>
				<span>{{$contador->count_label}}</span>
			</button>
			@endif
		@endif
	@endforeach
		<ul class="dropdown__menu">
		@foreach($user->energy_meters as $i => $contador)
			@if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, $type === 'indicadores' ? '16' : '15', $contador->id))
				@if($contador->id != $user->current_count->meter_id)
				<li class="dropdown__item">
					<a href="{{route('energymeter.change', ['user_id'=>$user->id, 'meter_id'=>$contador->id])}}">
						<i class="fa fa-clock-o"></i>
						<span>{{$contador->count_label}}</span>
					</a>
				</li>
				@endif
			@endif
		@endforeach
		</ul>
	</div>
</div>
@endsection

@section('content')
<div id="myTabContent" class="row">
	<div style="display: none;" >
		<div>
			<div class="container"   style="width:100%; display: inline-block">
				<div class="row">
					<div class="col">
						<h5 style="text-align: center;">Informe Producción<h5>
					</div>
					<div class="col">
						<img class="float-right" width="60" height="60" src="{{asset('images/Logo_WEB_Submeter.png')}}">
					</div>
				</div>
			</div>
			<div class="col-12">
				<table id="pdf_encabezado" class="col-12">
					<tr>
						<th class="text-left font-weight-bold">Intervalo</th>
						<td>Desde {{$date_from}} hasta {{$date_to}}</td>
					</tr>
				</table>
				<br>
				<br>
			</div>
		</div>
	</div>
	<div class="column">
		<div class="" style="width: 100%">
			<div class="card card-custom">
				<div class="card-header card-header-icon bg-white">
					<div class="card-icon bg-success text-white display-5">
						<i class="fa fa-calendar"></i>
					</div>
					<div class="card-row">
						<div class="input-group d-flex py-0">
							<div class="input-group-prepend">
								<span class="input-group-text bg-transparent font-weight-bold border-0">Intervalo:</span>
							</div>
							<label for="staticEmail" class="col-12 col-lg-8 col-form-label d-lg-inline"><h6>Desde {{$date_from}} hasta {{$date_to}}</h6></label>
						</div>
					</div>
				</div>
				<div class="card-body pt-3 pb-1 d-inline d-lg-none">
					<h6 class="card-title">
							@if(isset($domicilio->suministro_del_domicilio))
									{{$domicilio->suministro_del_domicilio}}
							@else
									Sin ubicación
							@endif
					</h6>
				</div>
			</div>
		</div>
	</div>
	<div class="column column-custom">
		@foreach($configurations as $config)
			<div data-chart-indicator="{{$config->id}}"
				data-type="{{$type}}"
				data-counter-name="{{$contador2->count_label}}"
				data-chart-indicator-name="{{$config->name}}"
				class="col-12"> 
			</div>
		@endforeach
	</div>								
</div>

<div class="content-main content-main-responsive col-md-12 pl-2 pr-4 content-90 gray-bg">
	<div class="content-mid">				
		<div class="grid_3 col-md-12">
			<div class="text-right"></div>						 
		</div>				
	</div>
	<div class="content-bottom">
		@include('Dashboard.modals.modal_intervalos4')
	</div>
	<form method="post" id="form-pdf" action="{{route('exportacion.pdf',['titulo'=>$titulo,'date_from'=>$date_from,'date_to'=>$date_to,'contador_label'=>$contador->count_label])}}">
		{{ csrf_field() }}
	</form>
</div>
@endsection

@section('otherlinks')
<style>
	.intervalo-izquierda {
		width: 50%;
	}
	.intervalo-izquierda .btn > i, 
	.intervalo-izquierda .btn > div{
		display: inline-block;
	}
	.d-none {
		display: none !important;
	}
	.intervalo-derecha {
		width: 50%;
    	text-align: right;
	}
	.modal-backdrop.fade.show{
		width: 100%;
		height: 100%;
		position: fixed;
		z-index: 999;
		background-color: #888d;
		left: 0;
		top: 0;
	}
	.modal{
		z-index: 9999;
	}
	body.modal-open .modal-header{
		min-height: 2.5rem;
		padding: 1.5rem 0.75rem;
		font-weight: 100;
		position: relative;
		border-bottom: 1px solid var(--clr-accent);
	}
	body.modal-open .modal-header h3{
		font-size: 1.5rem;
	}
	body.modal-open .close {
		position: absolute;
		right: 0;
		top: 0;
		height: 2.5rem;
		width: 2.5rem;
		font-weight: bold;
		border: 0;
		border-top-right-radius: var(--border-rad);
		background-color: inherit;
		outline: none;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
		cursor pointer;
	}
	body.modal-open .close:hover, 
	body.modal-open .close:focus{
		background-color: #000a;
    	color: #fff;
	}
	body.modal-open .modal-body{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 0.375rem 0.75rem;
		padding-bottom: 1.5rem;
	}
	body.modal-open .modal-footer{
		padding: 0.375rem 0.75rem;
		display: flex;
		justify-content: center;
	}
	body.modal-open .modal-footer .btn-default {
		margin-right: 4px;
	}
	.content.counters{
		position: relative;
	}
	.float-right-custom-1, 
	.float-right-custom-2{
		position: absolute;
		right: 8px;
		top: 12px;
	}
	.float-right-custom-2{
		right: 185px;
	}
	.card-custom {
		max-width: 50%;
		position: relative;
		display: -webkit-box;
		display: -ms-flexbox;
		display: inline-flex;
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-ms-flex-direction: column;
		flex-direction: column;
		min-width: 0;
		word-wrap: break-word;
		background-color: #fff;
		background-clip: border-box;
		border: 1px solid rgba(0,0,0,.125);
		border-radius: .25rem;
	}
	.card-custom .card-header{
		padding: .75rem 1.25rem;
		margin-bottom: 0;
		background-color: #fff;
		border-bottom: 1px solid rgba(0,0,0,.125);
		border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
	}
	.card-custom .card-icon{
		font-size: 1.7rem;
		font-weight: 300;
		line-height: 1.2;
		border-radius: 3px;
		background-color: #999;
		padding: 15px;
		margin-top: -30px;
		margin-right: 15px;
		float: left;
		color: #fff;
    	background-color: #28a745;
	}
	.card-custom .card-row{
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		margin-right: -15px;
		margin-left: -15px;
		margin-top: 8px;
	}
	.card-custom .card-row h6{
		font-size: 1rem;
		margin: 0;
		margin-left: 10px;
	}
	.d-lg-none{
		display: none;
	}
	.column.column-custom{
		width: 100%;
		margin-top: 0;
	}
	.column.column-custom div[data-chart-indicator="1"]{
		margin-top: 0;
	}
	.column-custom .col-12{
		width: 100%;
	}
	.column-custom .card{
		margin-top: 1.5rem;
		margin-bottom: 1.5rem;
		position: relative;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-orient: vertical;
		-webkit-box-direction: normal;
		-ms-flex-direction: column;
		flex-direction: column;
		min-width: 0;
		word-wrap: break-word;
		background-color: #fff;
		background-clip: border-box;
		border: 1px solid rgba(0,0,0,.125);
		border-radius: .25rem;
	}
	.column-custom .card .card-body{
		-webkit-box-flex: 1;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto;
	}
	.column-custom .card .card-body .card-title{
		font-weight: bold;
		-webkit-box-flex: 0;
		-ms-flex: 0 0 66.666667%;
		flex: 0 0 92%;
		font-size: 24px;
		margin-bottom: 1rem;
	}
	.column-custom .card .card-body .col-4.text-right{
		margin-top: 0.85rem;
	}
	.column-custom .plot-tab .row.mt-3.mb-3 .col-6{
		width: 50%;
	}
	.column-custom .plot-tab .row.mt-3.mb-3 .col-6:last-child{
		text-align: right;
	}
	.column-custom #totals_1{
		margin-top: 1rem;
	}
	.column-custom .justify-content-md-center {
		-webkit-box-pack: center!important;
		-ms-flex-pack: center!important;
		justify-content: center!important;
	}
	.column-custom .bg-submeter-4 {
		background-color: #004165;
	}
	.column-custom .table {
		width: 100%;
		max-width: 100%;
		margin-bottom: 1rem;
		background-color: transparent;
	}
	.column-custom .table-bordered {
		border: 1px solid #dee2e6;
		border-collapse: collapse;
	}
	.column-custom .table-light, 
	.column-custom .table-light>td, 
	.column-custom .table-light>th {
		background-color: #fdfdfe;
		border-collapse: collapse;
    	margin-top: 15px;
	}
	.column-custom .table-bordered thead td, 
	.column-custom .table-bordered thead th {
		border-bottom-width: 2px;
	}
	.column-custom .table-bordered td, 
	.column-custom .table-bordered th {
		border: 1px solid #dee2e6;
	}
	.column-custom .table td, 
	.column-custom .table > tbody > tr > td, 
	.column-custom .table > tbody > tr > th, 
	.column-custom .table > tfoot > tr > td, 
	.column-custom .table > tfoot > tr > th, 
	.column-custom .table > thead > tr > td, 
	.column-custom .table > thead > tr > th {
		padding: 15px;
		/* padding: 1px; */
		font-size: 0.9em;
		color: #272822;
		border-top: none !important;
		vertical-align: middle !important;
	}
	.column-custom .p-3{
		padding: 1rem;
	}
	.nav-pills-inline {
		max-width: 75%;
	}

	@media screen and (max-width: 1312px){
		.nav-pills-inline {
			max-width: 50%;
		}
	}
	@media screen and (max-width: 767px){
		.card-custom{
			max-width: 100%;
		}
		.card-custom .card-row h6{
			font-size: 0.8rem;
			margin-top: 3px;
		}
		.column-custom .card .card-body .card-title{
			font-size: 16px;
			flex: 0 0 75%;
		}
		.column-custom .card .card-body .col-4.text-right{
			margin-top: 0.55rem;
		}
		.column-custom .p-3{
			padding: 1rem 0.5rem;
		}
		.column-custom #totals_1, 
		.column-custom .table-detail.mt-5, 
		.column-custom #totals_2, 
		.column-custom #totals_3, 
		.column-custom #totals_4, 
		.column-custom #totals_5, 
		.column-custom #totals_6, 
		.column-custom #totals_7{
			width: 100% !important;
		}
		.column-custom .justify-content-md-center{
			overflow-x: scroll;
		}
	}
	@media screen and (max-width: 550px){
		.content.counters .float-right-custom-1, 
		.content.counters .float-right-custom-2{
			position: unset;
			margin-bottom: 8px;
		}
		.content.counters .float-right-custom-2{
			margin-left: 8px;
		}
		.nav-pills-inline {
			max-width: 100%;
		}
	}
	@media screen and (max-width: 469px){
		.intervalo-izquierda, 
		.intervalo-derecha{
			width: 100%;
			text-align: center;
		}
		.intervalo-derecha{
			margin-top: 8px;
		}
		.content.counters{
			align-items: center;
			justify-content: center;
		}
		.card-custom .card-header{
			padding-left: 5px;
			padding-right: 5px;
		}
		.card-custom .card-icon{
			font-size: 0.5rem;
			padding: 5px;
		}
		.card-custom .card-icon i{
			font-size: 20px;
		}
		.card-custom .card-row{
			margin-left: 15px;
			margin-top: 0;
		}
		.column-custom .card .card-body .card-title{
			font-size: 14px;
		}
	}
</style>
@endsection

@section('scripts')
<script src="{{asset('js/canvas.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
	$(document).ready(function(){
		$(".export-pdf-button").click(handlePDFRequest);
		createPlots();
	});

	var tableToExcel = (function () {
		var uri = 'data:application/vnd.ms-excel;base64,',
		template = "{{$template}}",
		base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) },
		format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
		return function (table, name, filename) {
			if (!table.nodeType) table = document.getElementById(table)
			var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
			document.getElementById("dlink").href = uri + base64(format(template, ctx));
			document.getElementById("dlink").download = filename;
			document.getElementById("dlink").click();
		}
	})()

	function renderPlots(){
		var plots = $(".plot-tab");
		for (var i = 0; i < plots.length; i++) {
			var plot = $(plots[i]);
			var chart = plot.data("chart");
			if(chart !== undefined){
				chart.render();
			}
		}
	}

	function togglePlotsDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		var cnt = $(e.chart.container).closest(".plot-tab");
		if(cnt.length > 0) {
			chart = cnt.data("chart");
			if(chart != undefined) {
				chart.render();
			}
		}
	}

	CanvasJS.addCultureInfo("es", {
		decimalSeparator: ",",// Observe ToolTip Number Format
		digitGroupSeparator: "."
	});

	function createPlots(){


			var nameProduction = '';
			var plots = $(".production-plot");

			for(var iK = 0; iK < plots.length; iK++) {
					console.log(nameProduction[iK].name);
					var plot = $(plots[iK]);
					var cntPlot = plot.find(".plot-container");
					console.log(plots);
					var labels = $.parseJSON(plot.find(".plot-labels").val());


					var seriesValues = plot.find("[name='data_plot']");
					var seriesColors = plot.find("[name='color']");
					var seriesName = plot.find("[name='display_name']");
					var seriesNumberType = plot.find("[name='number_type']");
					var seriesAuxLabels = plot.find("[name='units']");
					var seriesDecimals = plot.find("[name='decimals']");

					var data = new Array();
					var dataPlot = new Array();
					for(var i = 0; i < seriesValues.length; i++) {
							var serieVal = $(seriesValues[i]).val();
							serieVal = $.parseJSON(serieVal);
							var seriedata = new Array();
							for(j = 0; j < serieVal.length; j++) {
									var d = {
													y : serieVal[j],
													x : j,
													label: labels[j],
									};
									if(nameProduction[iK].chart_type == 'pie')
									{
											d.color = dynamicColors();
									}
									seriedata.push(d);
							}
							data[i] = seriedata;

							var labAux = $(seriesAuxLabels[i]);
							var tooltip = "{name}: {y} " + labAux.val();

							var ntype = parseInt($(seriesNumberType[i]).val());
							var decimal = parseInt($(seriesDecimals[i]).val());
							var format = "0";

							if(isNaN(ntype)) {
									ntype = 2;
							}

							if(isNaN(decimal)) {
									decimal = 0;
							}

							if(ntype == 2) {
									format = "##,##0";
							}
							else {
									var intp = "##,##0";
									var fpart = "";
									fpart = fpart.padEnd(decimal, "0");
									format = intp + "." + fpart;
							}

							var conf = {
									cursor: "zoom-in",
									type: nameProduction[iK].chart_type,
									showInLegend: nameProduction[iK].chart_type != 'pie' ? true: false,
									visible: true,
									bevelEnabled: true,
									markerSize: 0,
									name: $(seriesName[i]).val(),
									legendColor: $(seriesColors[i]).val(),
									lineColor: $(seriesColors[i]).val(),
									color:  $(seriesColors[i]).val(),
									legendMarkerColor: $(seriesColors[i]).val(),
									toolTipContent: tooltip,
									dataPoints: data[i],
									yValueFormatString: format
							};
							dataPlot.push(conf);
					}

					var titulo = plot.find(".plot-name").val();  
					var conta = "{{ $contador2->count_label }}";
					var date_to = "{{ $date_to }}";
					var date_from ="{{ $date_from }}";
					
					var chart = new CanvasJS.Chart(cntPlot.attr("id"), {
							animationEnabled: false,
							zoomEnabled:true,
							culture: "es",
							theme: "light2",
							//height: 460,
							title:{
									text: plot.find(".plot-name").val(),
									fontSize: 18,
									margin: 50,
									fontColor: "#004165"
							},
							exportFileName: titulo+"-"+conta+"-"+date_from+"-"+date_to,
							exportEnabled: true,
							axisX: {
									//minimum:0,
									titleFontSize: 12,
									titleFontColor: "#004165",
									lineColor: "#004165",
									labelFontColor: "#004165",
									interval: dataPlot[0].dataPoints.length > 24 ? 1 : 1, //# @Leo W* vamos asegurarnos de que cuando los intervaloes sean mas de 2 puntos no se encimen en el eje de las X
									tickColor: "#004165"
									},
							axisY: {
									lineColor: "#004165",
									labelFontColor: "#004165",
									tickColor: "#004165",
							},
							toolTip: {
									shared: "true"
							},
							legend:{
									cursor:"pointer",
									itemclick : togglePlotsDataSeries
							},
							data: dataPlot
					});
					plot.data("chart", chart);
					cntPlot.data("chart", chart);
					plot.data("chart_rendered", 0);
					chart.render();
					plot.data("chart_rendered", 1);

			}
	}

	var handlePDFRequest = function(event) {
		event.preventDefault();
		var container = $(this).closest(".production-container");
		exportProdPdf(container);
	}

	var exportProdPdf = function(objContainer){
		var idxBreak = "";
		var tokenInput = $("#form-pdf input[name='_token']")[0].outerHTML;
		$("#form-pdf").html("");
		$("#form-pdf").append(tokenInput);

		var header = objContainer.find(".pdf-header")[0].outerHTML;

		var input = $("<input name='elements[]' type='hidden' value='"+btoa(unescape(encodeURIComponent(header)))+"' />");
		var type = $("<input name='type_elements[]' value='2' type='hidden' />");
		$("#form-pdf").append(input);
		$("#form-pdf").append(type);

		var objActive = objContainer.find(".plot-container");
		var width = parseInt(objActive.width());
		var height = 350;

		var cntChart = objContainer.find(".plot-tab");
		var handleCharts = [];
		var dataCharts = [];

		var idxElement = 1;

		var canvas = objContainer.find(".canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
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

		var htmlData = objContainer.find(".export-pdf");

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
	};

	function anterior()
	{
		$('#before_navigation').val("-1");
		$("#form_navigation").submit();
	}
	function siguiente()
	{
		$('#before_navigation').val("1");
		$("#form_navigation").submit();
	}
	function volver()
	{
		$('#before_navigation').val("0");
	}

	var dynamicColors = function() {
		var r = Math.floor(Math.random() * 255);
		var g = Math.floor(Math.random() * 255);
		var b = Math.floor(Math.random() * 255);
		return "rgb(" + r + "," + g + "," + b + ")";
	};
</script>
@endsection
