@extends('Dashboard.layouts.global4')
@section('content')

<div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
    <div class="banner col-md-12 mb-4 mr-3">
        <div class="row">
            <div class="col-12 col-lg-6 text-center text-lg-left pl-4">
                <button type="button" class="btn btn-middle btn-primary-submeter" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus"></i>  <div class="d-none d-sm-inline">Intervalos de Períodos ({{$label_intervalo}})</div> <div class="d-inline d-sm-none">{{$label_intervalo}}</div>
                </button>
            </div>
            <div class=" col-12 col-lg-6 text-center text-lg-right mt-1 mt-sm-3 mt-lg-1 pr-lg-5 font-nav-buttons">
                @if(true)
                    <form id="form_navigation" action="{{route('config.navigation')}}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="option_interval" value="9">
                        <input type="hidden" name="label_intervalo" value="{{$label_intervalo}}">
                        <input type="hidden" name="date_from_personalice" value="{{$date_from}}">
                        <input type="hidden" name="date_to_personalice" value="{{$date_to}}">
                        <input type="hidden" name="before_navigation" id="before_navigation" value="0">
                        <button type="submit" class="btn btn-primary-submeter btn-arrow-left" onclick="anterior()">Ant.</button>
                        @if(isset(Session::get('_flash')['current_date']))
                            <button type="button" class="btn btn-link">{{Session::get('_flash')['current_date']}}</button>
                        @else
                            <button type="button" class="btn  btn-link"> {{$label_intervalo}} </button>
                        @endif
                        <button type="submit" class="btn  btn-primary-submeter btn-arrow-right" onclick="siguiente()">Sig.</button>
                    </form>
                @endif
            </div>
            @if(isset($ctrl) && $ctrl == 1)
                    <a href="{!! route('admin.users',[2, $id]) !!}" class="btn btn-info btn-lg"><i class="fa fa-undo"></i></a>
            @endif
        </div>
    </div>
    <div class="content-mid">
        
        <div class="grid_3 col-md-12">
            <a class="btn btn-sm float-right btn-outline-primary" href="{!! route('production.list',[]) !!}" class=" hvr-bounce-to-right">
                <i class="fa fa-cogs"></i>
                Editar configuración
            </a>
            <ul class="nav nav-header nav-pills contador-pill text-center">
                @foreach($user->energy_meters as $i => $contador)
                    @if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, 6, $contador->id))
                        @if($contador->id == $user->current_count->meter_id)
                            <li class="nav-item mx-1 mt-1 mt-sm-0">
                                <a class="nav-link bg-submeter-3 active text-white" href="{{route('energymeter.change', ['user_id'=>$user->id, 'meter_id'=>$contador->id])}}">
                                    <i class="fa fa-clock"></i>{{$contador->count_label}}
                                </a>
                            </li>
                        @else
                            <li class="nav-item mx-1 mt-1 mt-sm-0">
                                <a class="nav-link bg-submeter-5" href="{{route('energymeter.change', ['user_id'=>$user->id, 'meter_id'=>$contador->id])}}">
                                    <i class="fa fa-clock"></i>{{$contador->count_label}}
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            </ul>
            <div class="text-right">
                
            </div>
             
            <div id="myTabContent" class="tab-content">
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
                <div class="row mt-4">
                    <div class="col-12 col-md-6 mt-4 mt-md-0">
                        <div class="card">
                            <div class="card-header card-header-icon bg-white">
                                <div class="card-icon bg-success text-white display-5">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="row">
                                    <div class="input-group d-flex py-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent font-weight-bold border-0">Intervalo:</span>
                                        </div>
                                        <label for="staticEmail" class="col-12 col-lg-8 col-form-label d-none d-lg-inline"><h6>Desde {{$date_from}} hasta {{$date_to}}</h6></label>
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
                <div class="row">
                    @if($productions && count($productions) > 0)
                        @foreach($productions as $index => $production)
                            <div class="col-12 mx-0 px-0 production-container">
                                <div class="row mx-0 px-0">
																	<div style="display: none;" >
                                    <div class="col-12 my-4 pdf-header">
																			<div class="row">
																				<div class="col">
																					<!-- sitio para icono de empresa -->
																				</div>
																				<div class="col">
																					<h5 style="text-align: center;">Produccion Submeter<h5>
																				</div>
																				<div class="col">
																				<img class="float-right" width="60" height="60" src="{{asset('images/Logo_WEB_Submeter.png')}}">
																				</div>
																			</div>
																		</div>
                                  </div>
																		<div class="col-12 my-4">
																			<div class="card">
																					<div class="card-body pt-3 pb-1">
																							<h4 class="card-title" style="font-weight:bold;">
																									{{ $production->name }}
																							</h4>
																					</div>
																			</div>
																		</div>
                                    @if($production->data_production)
                                        <div class="col-12 plot-tab mx-0 px-0 production-plot">
                                            @if(array_key_exists("production", $production->data_production))
                                                @if(array_key_exists("labels", $production->data_production["production"])
                                                    && array_key_exists("interval_values", $production->data_production["production"]["labels"]))
                                                        <input type="hidden" class="plot-labels" value='{!! json_encode($production->data_production["production"]["labels"]["interval_values"]) !!}' />
                                                        <input type="hidden" class="plot-name" value="{{ $production->name }}" />

                                                        <div class="col-md-12 graph-1 plot-tab">
                                                               <div class="grid-1">
                                                                            <div  id="graphTotal-{{ $index }}" class="plot-container" style="height: {{$production->chart_type == 'pie' ? '500px': '330px'}}; width: 100%;"></div>
                                                                  </div>
                                                        </div>
                                                @endif

                                                @if(array_key_exists("group_data", $production->data_production["production"]))
                                                    @foreach($production->data_production["production"]["group_data"] as $group_key => $config_values)
                                                        @if( in_array($config_values["show_type"], [2, 3, 6, 7]) )
                                                            <div class="col-12 plot-tab mx-0 px-0 plot-container-data">
                                                                <input type="hidden" name="color" value='{{ $config_values["color"] }} ' />
                                                                <input type="hidden" name="display_name" value='{{ $config_values["display_name"] }} ' />
                                                                <input type="hidden" name="number_type" value='{{ $config_values["number_type"] }} ' />
                                                                <input type="hidden" name="units" value='{{ $config_values["units"] }} ' />
                                                                <input type="hidden" name="decimals" value='{{ $config_values["decimals"] }} ' />
                                                                @if( array_key_exists("group_totals_table", $production->data_production["production"])
                                                                        && array_key_exists($group_key, $production->data_production["production"]["group_totals_table"])
                                                                    )
                                                                    <input type="hidden" name="data_plot" value = '{{ json_encode($production->data_production["production"]["group_totals_table"][$group_key]) }}' />
                                                                @else
                                                                    <input type="hidden" name="data_plot" value = '' />
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        </div>
<!--________________________________________________TABLES________________________________ -->
                                    <a id="dlink"  style="display:none;"></a>
                                    <div class="col-xs-12 col-md-12">
                                                    <div class="text-left" style="display: inline-block; margin-left: 10px;">

                                                  <button onclick="tableToExcel('{{ $production->id }}', 'name', 'Datos de Produccion')" class="btn btn-lg color-127 pull-left " style="font-size: 12pt;"> Exportar datos (CSV)</button>

                                                    </div>
                                            <button class="btn btn-lg color-127 float-right export-pdf-button" style="margin-bottom:15px" id="exportButton"> Generar PDF</button>
                                    </div>
																		<div style="display: none;" >
																		<div class="col-md-12 export-pdf">
																			<div style="margin:20px"></div>
																		</div>
																		</div>
                                    <div class="row col-md-12 justify-content-md-center">
                                     <div class="col-xl-8 export-pdf">
                                            @if(array_key_exists("group_data", $production->data_production["production"]))

                                                <table class="table table-bordered table-striped table-light text-center" id="{{ $production->id }}">

                                                       <thead class="bg-submeter-4">
                                                          <tr>
                                        @foreach($production->data_production["production"]["group_data"] as $group_key => $config_values)
                                            @if( in_array($config_values["show_type"], [4,5,6,7]) )
                                                @if( array_key_exists("group_totals", $production->data_production["production"])
                                                    && array_key_exists($group_key, $production->data_production["production"]["group_totals"])
                                                )
                                                          <th class="text-center text-white" style="vertical-align: middle;color:white;">{{ $config_values["display_name"] }}</th>
                                                 @endif
                                            @endif
                                         @endforeach
                                                          </tr>
                                                        </thead>


                                                    <tbody>
                                                          <tr>

                                            @foreach($production->data_production["production"]["group_data"] as $group_key => $config_values)
                                              @if( in_array($config_values["show_type"], [4,5,6,7]) )
                                                @if( array_key_exists("group_totals", $production->data_production["production"])
                                                    && array_key_exists($group_key, $production->data_production["production"]["group_totals"])
                                                )
                                                    <td>
                                                             <p>Tipo: {{ $config_values["type"] }}</p>
                                                                 @if($config_values["number_type"] == 2)
                                                             <p>
                                                                {{number_format($production->data_production["production"]["group_totals"][$group_key],0,',','.')}}
                                                                {{ $config_values["units"] }}
                                                             </p>
                                                            @else
                                                                <p>
                                                                    {{number_format($production->data_production["production"]["group_totals"][$group_key],$config_values["decimals"],',','.')}}
                                                                    {{ $config_values["units"] }}
                                                                </p>
                                                            @endif
                                                    </td>
                                                @endif
                                               @endif
                                            @endforeach

                                                          </tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                    </div>
                                  </div>
<!--________________________________________________TABLES________________________________ -->


                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
        </div>
        
    </div>
    <div class="content-bottom">
        @include('Dashboard.modals.modal_intervalos4')
    </div>
    
    <div class="copy">
        <p> &copy; 2020 Submeter 4.0. Todos los derechos reservados</p>
    </div>
    <form method="post" id="form-pdf" action="{{route('exportacion.pdf',['titulo'=>$titulo,'date_from'=>$date_from,'date_to'=>$date_to,'contador_label'=>$contador->count_label])}}">
        {{ csrf_field() }}
    </form>
</div>
@endsection

@section('scripts')
<script src="{{asset('js/canvas.js')}}"></script>

<script type="text/javascript">
<!--
    $(document).ready(function(){

        $(".export-pdf-button").click(handlePDFRequest);
        createPlots();
    });

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
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


        var nameProduction = <?php echo json_encode($productions); ?>;
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

//-->
</script>
@endsection
