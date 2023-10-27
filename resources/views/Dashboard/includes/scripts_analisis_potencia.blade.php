<script>

	var arrayEje = <?php echo json_encode($eje); ?>;
	var tipo_tarifa = <?php echo json_encode($tipo_tarifa); ?>;
	var arrayPotenciaDemandada = <?php echo json_encode($p_demandada); ?>;
	var arrayPotenciaOptima = <?php echo json_encode($p_optima); ?>;
	var arrayPotenciaContratada = <?php echo json_encode($p_contratada); ?>;

	var arrayPotenciaContratada_ochenta = <?php echo json_encode($p_85_contratada); ?>;
	var arrayPotenciaContratada_ciento = <?php echo json_encode($p_105_contratada); ?>;
	var totalD = <?php echo json_encode(number_format($totalD,0,',','.')); ?>;
	var totalC = <?php echo json_encode(number_format($totalC,0,',','.')); ?>;
	var dates = <?php echo json_encode($dates); ?>;
	var titulo = <?php echo json_encode($titulo); ?>;
	var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;

	var datapoins1 = [];
	var datapoins2 = [];
	var datapoins3 = [];
	var datapoins4 = [];
	var datapoins5 = [];

	var maxima = <?php echo json_encode(number_format($maxima_potencia,0,',','.')); ?>;
	var intervalo = <?php echo json_encode($label_intervalo); ?>;
	var date_to = <?php echo json_encode($date_to); ?>;
	var date_from = <?php echo json_encode($date_from); ?>;
	var aux_label = '';
	var aux_interval;
	var aux_Conteo = 0;
	// console.log(arrayPotenciaOptima);

	if(intervalo == 'Personalizado' && date_to !== date_from)
	{
		// console.log(dates, arrayEje);
		arrayEje = dates;
		// console.log(arrayEje);
	}

	for (var i = 0; i < arrayEje.length; i++) {

		
		 arrayEje[i] = arrayEje[i].substring(0, 5);


		datapoins1.push({ label:arrayEje[i], y: parseInt(arrayPotenciaDemandada[i]), color:"#004165" });
		datapoins2.push({ label:arrayEje[i], y: parseInt(arrayPotenciaContratada[i]), color:"#B9C9D0" });
		datapoins3.push({ label:arrayEje[i], y: parseInt(arrayPotenciaOptima[i]), color:"#7D9AAA" });
		if(tipo_tarifa != 1)
		{
			datapoins4.push({ label:arrayEje[i], y: parseInt(arrayPotenciaContratada_ochenta[i]), color:"#7D9AAA" });
			datapoins5.push({ label:arrayEje[i], y: parseInt(arrayPotenciaContratada_ciento[i]), color:"#7D9AAA" });
		}
		aux_Conteo++;
	}

	if(intervalo == "Ayer" || intervalo == "Hoy" || (intervalo == 'Personalizado' && date_to == date_from))
	{
		aux_label = "Hora: ";
		if(aux_Conteo < 24)
		{
			aux_interval = 1;
			for(var k = aux_Conteo; k < 24; k++)
			{
				if(k < 10)
				{
					datapoins1.push({ label: "0"+k+":00", y: 0, color:"#004165" },);
					datapoins2.push({ label: "0"+k+":00", y: 0, color:"#B9C9D0" },);
					datapoins3.push({ label: "0"+k+":00", y: 0, color:"#7D9AAA" });
					if(tipo_tarifa)
					{
						datapoins4.push({ label: "0"+k+":00", y: 0, color:"#7D9AAA" });
						datapoins5.push({ label: "0"+k+":00", y: 0, color:"#7D9AAA" });
					}
				}else{
					if(k >= 10)
					{
						datapoins1.push({ label: k+":00", y: 0, color:"#004165" },);
						datapoins2.push({ label: k+":00", y: 0, color:"#B9C9D0" },);
						datapoins3.push({ label: k+":00", y: 0, color:"#7D9AAA" });
						if(tipo_tarifa)
						{
							datapoins4.push({ label: k+":00", y: 0, color:"#7D9AAA" });
							datapoins5.push({ label: k+":00", y: 0, color:"#7D9AAA" });
						}
					}
				}
			}
		}
	}else{
		if(intervalo == "Mes Actual" || intervalo == "Mes Anterior")
		{
			aux_label = "Día: ";
			aux_interval = 1;
			var aux_day = date_to.split('-');
			if(aux_Conteo < aux_day[2])
			{
				for(var k = aux_Conteo+1; k <= parseInt(aux_day[2]); k++)
				{
					// datapoins1.push({ label: k, y: 0, color:"#004165" });
					// datapoins2.push({ label: k, y: 0, color:"#B9C9D0" });
					// datapoins3.push({ label: k, y: 0, color:"#7D9AAA" });
					// if(tipo_tarifa)
					// {
					// 	datapoins4.push({ label: k, y: 0, color:"#7D9AAA" });
					// 	datapoins5.push({ label: k, y: 0, color:"#7D9AAA" });
					// }
					if(arrayEje[aux_Conteo-1] < k)
					{
						datapoins1.push({ label: k, y: 0, color:"#004165" });
						datapoins2.push({ label: k, y: 0, color:"#B9C9D0" },);
						datapoins3.push({ label: k, y: 0, color:"#7D9AAA" });
					}else{
						if(aux_Conteo == 0)
						{
							datapoins1.push({ label: k, y: 0, color:"#004165" });
							datapoins2.push({ label: k, y: 0, color:"#B9C9D0" },);
							datapoins3.push({ label: k, y: 0, color:"#7D9AAA" });
						}
					}
				}
			}
		}else{
			if(intervalo == "Semana Actual" || intervalo == "Semana Anterior")
			{
				var aux_dias = [];
				aux_dias[0] = 'Lunes';
				aux_dias[1] = 'Martes';
				aux_dias[2] = 'Miércoles';
				aux_dias[3] = 'Jueves';
				aux_dias[4] = 'Viernes';
				aux_dias[5] = 'Sábado';
				aux_dias[6] = 'Domingo';

				if(aux_Conteo < 7)
				{
					for(var k = aux_Conteo; k < 7; k++)
					{
						datapoins1.push({ label: aux_dias[k], y: 0, color:"#004165" },);
						datapoins2.push({ label: aux_dias[k], y: 0, color:"#B9C9D0" },);
						datapoins3.push({ label: aux_dias[k], y: 0, color:"#7D9AAA" });
						if(tipo_tarifa)
						{
							datapoins4.push({ label: aux_dias[k], y: 0, color:"#7D9AAA" });
							datapoins5.push({ label: aux_dias[k], y: 0, color:"#7D9AAA" });
						}
					}

				}
			}else{
				if(intervalo == "Ultimo Trimestre" || intervalo == "Último Año")
				{
					aux_label = "Mes: ";
				}else{
					if(intervalo == "Personalizado" && date_to == date_from)
					{
						aux_label = "Hora: ";
					}else{
						if(intervalo == "Personalizado" && date_to != date_from)
						{
							aux_label = "Fecha: ";
						}
					}
				}
			}
		}
	}

	//console.log(arrayPotenciaOptima_aux);
	CanvasJS.addCultureInfo("es",
    {
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    });

	var chart = new CanvasJS.Chart("AnalisisPotencia_"+contador, {
		animationEnabled: false,
		culture: "es",
		theme: "light2",
		title:{
			text: "Análisis Potencia Demandada - Contratada",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "AnálisisPotenciaDemandada-Contratada-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisX: {
			title: 'Máxima Potencia Demandada: '+maxima+' kW',
			titleFontSize: 12,
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			interval: aux_interval,
			tickColor: "#004165"
			},
		axisY: {
			suffix: " kW",
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		toolTip: {
			shared: "true"
		},
		legend:{
			cursor:"pointer",
			itemclick : toggleDataSeries
		},
		data: [{
			type: "spline",
			visible: true,
			showInLegend: true,
			bevelEnabled: true,
			toolTipContent: aux_label+"{label} <br>{name}: {y}  kW",
			markerSize: 0,
			// yValueFormatString: "###0.## kW",
			name: "Potencia Demandada",
			legendMarkerColor: "#004165",
			legendColor: '#004165',
			lineColor: "#004165",
			color: "#004165",
			dataPoints: datapoins1
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "Potencia Contratada",
			legendColor: '#B9C9D0',
			lineColor: "#B9C9D0",
			color: "#B9C9D0",
			legendMarkerColor: "#B9C9D0",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins2
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "Potencia Óptima",
			legendColor: '#FB244C',
			lineColor: "#7D9AAA",
			color: "#7D9AAA",
			legendMarkerColor: "#7D9AAA",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins3
		}

		]
	});
	chart.render();

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		chart.render();
	}

	// CONTADORES DE 3 PERÍODOS

</script>

<script>
	var chart2 = new CanvasJS.Chart("AnalisisPotencia3P_"+contador, {
		animationEnabled: false,
		culture: "es",
		theme: "light2",
		title:{
			text: "Análisis Potencia Demandada - Contratada",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "AnálisisPotenciaDemandada-Contratada-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisX: {
			titleFontSize: 12,
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			interval: aux_interval,
			tickColor: "#004165"
			},
		axisY: {
			suffix: " kW",
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		toolTip: {
			shared: "true"
		},
		legend:{
			cursor:"pointer",
			itemclick : toggleDataSeries
		},
		data: [{
			type: "spline",
			visible: true,
			showInLegend: true,
			bevelEnabled: true,
			toolTipContent: aux_label+"{label} <br>{name}: {y}  kW",
			markerSize: 0,
			// yValueFormatString: "###0.## kW",
			name: "Potencia Demandada",
			legendMarkerColor: "#004165",
			legendColor: '#004165',
			lineColor: "#004165",
			color: "#004165",
			dataPoints: datapoins1
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "Potencia Contratada",
			legendColor: '#B9C9D0',
			lineColor: "#B9C9D0",
			color: "#B9C9D0",
			legendMarkerColor: "#B9C9D0",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins2
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "Potencia Óptima",
			legendColor: '#FB244C',
			lineColor: "#7D9AAA",
			color: "#7D9AAA",
			legendMarkerColor: "#7D9AAA",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins3
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "85% Pot. Contratada",
			legendColor: '#FB244C',
			lineColor: "#FE2E2E",
			color: "#FE2E2E",
			legendMarkerColor: "#FE2E2E",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins4
		},
		{
			type: "spline",
			showInLegend: true,
			visible: true,
			bevelEnabled: true,
			// yValueFormatString: "###0.## kW",
			markerSize: 0,
			name: "105% Pot. Contratada",
			legendColor: '#FB244C',
			lineColor: "#FE2E2E",
			color: "#FE2E2E",
			legendMarkerColor: "#FE2E2E",
			toolTipContent: "{name}: {y}  kW",
			dataPoints: datapoins5
		}

		]
	});
	chart2.render();

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		chart2.render();
	}
</script>

<script>
	if(tipo_tarifa == 1){
		var canvas1 = $("#AnalisisPotencia_"+contador+" .canvasjs-chart-canvas").get(0);
	}else{
		var canvas1 = $("#AnalisisPotencia3P_"+contador+" .canvasjs-chart-canvas").get(0);
	}

	var dataURL1 = canvas1.toDataURL();

	var empresa = <?php echo json_encode($user->name) ?>;
	var email = <?php echo json_encode($user->email) ?>;
	var date_from = <?php echo json_encode($date_from) ?>;
	var date_to = <?php echo json_encode($date_to) ?>;
	var conta = <?php echo json_encode($contador_label) ?>;
	//console.log(dataURL);

	$("#exportButton").click(function(){
	    var pdf = new jsPDF("l", "mm", "a4");
	    pdf.setTextColor(51, 51, 51);
	    pdf.text(20, 20, 'Empresa: '+empresa);
	    pdf.setFontSize(11);
	    // pdf.text(20, 30, 'Ubicación: '+ubicacion);
	    pdf.text(20, 37, 'Contador: '+conta);
	    pdf.text(20, 44, 'Email: '+email);
	    pdf.text(20, 51, 'Intervalo: Desde '+date_from+' hasta '+date_to);
	    //pdf.text(20, 30, "Contador: "+contador);
		//pdf.text(20, 40, ubicacion);
	    var width = pdf.internal.pageSize.width;
		var height = pdf.internal.pageSize.height;
	    pdf.addImage(dataURL1, 'JPEG', 20, 60, 250, 0);
	    //pdf.text(20, height/4+20+30+80, "Total Consumo Inductiva: "+ totalInduc +" kVArh");
	    //pdf.text(20, height/4+20+30+90, "Total Consumo Capacitiva: "+ totalCap +" kVArh");
	    pdf.save("Analisis_Potencia.pdf");
	});
</script>
