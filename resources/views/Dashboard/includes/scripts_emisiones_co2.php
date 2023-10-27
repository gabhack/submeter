
<script>
	var arrayEmisiones = <?php echo json_encode($emisiones); ?>;
	var arrayEmisionesAntes = <?php echo json_encode($emisiones_antes); ?>;
	var arrayEje = <?php echo json_encode($eje); ?>;
	var arrayEjeAntes = <?php echo json_encode($eje_antes); ?>;
	var arrayEmisiones2 = <?php echo json_encode($emisiones2); ?>;
	var arrayEmisiones2Antes = <?php echo json_encode($emisiones2_antes); ?>;
	var dates = <?php echo json_encode($dates); ?>;
	var contador = <?php echo json_encode(implode('_', explode(' ', $contador_label))) ?>;
	var datapoins1 = [];
	var datapoins2 = [];
	var empresa = "Empresa: "+<?php echo json_encode($user->name); ?>;
	var ubicacion = "Ubicación: "+<?php echo json_encode($direccion); ?>;
	var totalEmi = <?php echo json_encode(number_format($total_emisiones,'0',',','.')); ?>;

	var titulo = <?php echo json_encode($titulo); ?>;
	var intervalo = <?php echo json_encode($label_intervalo); ?>;
	var date_to = <?php echo json_encode($date_to); ?>;
	var date_from = <?php echo json_encode($date_from); ?>;
	var aux_label = '';
	var aux_interval;
	var aux_Conteo1 = 0;
	var aux_Conteo2 = 0;
	var axisXTypeValue = "primary";
	var titleAxisX =  <?php echo json_encode($titleAxisX); ?>;



	console.log(arrayEmisiones)
	console.log(arrayEmisionesAntes)
	console.log(arrayEje)
	console.log(arrayEjeAntes)
	console.log(arrayEmisiones2)
	console.log(arrayEmisiones2Antes)
	console.log(dates)
	
	if(intervalo == 'Personalizado' && date_to !== date_from)
	{

		arrayEje = dates;

	}

	if(intervalo == "Personalizado" || intervalo == "Ayer" || intervalo == "Hoy" )
	{

			for (var i = 0; i < arrayEmisiones.length; i++) {
				datapoins1.push({ label: arrayEmisiones[i]['eje'], y: parseFloat(arrayEmisiones[i]['emisiones']), color:"#004165" });
				aux_Conteo1++;
		}
	}else{
			for (var i = 0; i < arrayEje.length; i++) {
				datapoins1.push({ label: arrayEje[i], y: parseFloat(arrayEmisiones2[i]), color:"#004165" });
				aux_Conteo1++;
		}
	}


	if(intervalo == "Personalizado" || intervalo == "Ayer" || intervalo == "Hoy" )
	{
		for (var i = 0; i < arrayEmisionesAntes.length; i++) {
			datapoins2.push({ label: arrayEmisionesAntes[i]['eje'], y: parseFloat(arrayEmisionesAntes[i]['emisiones']), color:"#808080" });
			aux_Conteo2++;
		}
	}else{

			for (var i = 0; i < arrayEjeAntes.length; i++) {
				datapoins2.push({ label: arrayEjeAntes[i], y: parseFloat(arrayEmisiones2Antes[i]), color:"#808080" });
				aux_Conteo2++;
		}
	}



	if(intervalo == "Ayer" || intervalo == "Hoy")
	{
		aux_label = "Hora: ";

			if(aux_Conteo1 < 1)
			{

				datapoins1.push({ label: "00:15", y: 0, color:"#004165" },);
				datapoins1.push({ label: "00:30", y: 0, color:"#004165" },);
				datapoins1.push({ label: "00:45", y: 0, color:"#004165" },);
				for(var k = aux_Conteo1+1; k < 24; k++)
				{
					if(k < 10)
					{
						datapoins1.push({ label: "0"+k+":00", y: 0, color:"#004165" },);
						datapoins1.push({ label: "0"+k+":15", y: 0, color:"#004165" },);
						datapoins1.push({ label: "0"+k+":30", y: 0, color:"#004165" },);
						datapoins1.push({ label: "0"+k+":45", y: 0, color:"#004165" },);
					}else{
						if(k >= 10)
						{
							datapoins1.push({ label: k+":00", y: 0, color:"#004165" },);
							datapoins1.push({ label: k+":15", y: 0, color:"#004165" },);
							datapoins1.push({ label: k+":30", y: 0, color:"#004165" },);
							datapoins1.push({ label: k+":45", y: 0, color:"#004165" },);
						}
					}

				}
				datapoins1.push({ label: "23:59", y: 0, color:"#004165" },);
			}

			if(aux_Conteo2 < 1)
			{

				datapoins2.push({ label: "00:15", y: 0, color:"#808080" },);
				datapoins2.push({ label: "00:30", y: 0, color:"#808080" },);
				datapoins2.push({ label: "00:45", y: 0, color:"#808080" },);
				for(var k = aux_Conteo2+1; k < 24; k++)
				{
					if(k < 10)
					{
						datapoins2.push({ label: "0"+k+":00", y: 0, color:"#808080" },);
						datapoins2.push({ label: "0"+k+":15", y: 0, color:"#808080" },);
						datapoins2.push({ label: "0"+k+":30", y: 0, color:"#808080" },);
						datapoins2.push({ label: "0"+k+":45", y: 0, color:"#808080" },);
					}else{
						if(k >= 10)
						{
							datapoins2.push({ label: k+":00", y: 0, color:"#808080" },);
							datapoins2.push({ label: k+":15", y: 0, color:"#808080" },);
							datapoins2.push({ label: k+":30", y: 0, color:"#808080" },);
							datapoins2.push({ label: k+":45", y: 0, color:"#808080" },);
						}
					}

				}
				datapoins2.push({ label: "23:59", y: 0, color:"#808080" },);
			}
	}else{
		if(intervalo == "Mes Actual" || intervalo == "Mes Anterior")
		{
			aux_label = "Día: ";
			aux_interval = 1;
			var aux_day = date_to.split('-');

			if(aux_Conteo1 < aux_day[2])
			{
				for(var k = aux_Conteo1+1; k <= parseInt(aux_day[2]); k++)
				{
					if(arrayEje[aux_Conteo1-1] < k)
					{
						datapoins1.push({ label: k, y: 0, color:"#004165" });

					}else{
						if(aux_Conteo1 == 0)
						{
							datapoins1.push({ label: k, y: 0, color:"#004165" });

						}
					}
				}
			}

			if(aux_Conteo2 < aux_day[2])
			{
				for(var k = aux_Conteo2+1; k <= parseInt(aux_day[2]); k++)
				{
					if(arrayEje[aux_Conteo2-1] < k)
					{
						datapoins2.push({ label: k, y: 0, color:"#004165" });

					}else{
						if(aux_Conteo2 == 0)
						{
							datapoins2.push({ label: k, y: 0, color:"#004165" });

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

				if(aux_Conteo1 < 7)
				{
					for(var k = aux_Conteo1; k < 7; k++)
					{
						datapoins1.push({ label: aux_dias[k], y: 0, color:"#004165" });
					}


				}
				if(aux_Conteo2 < 7)
				{
					for(var k = aux_Conteo2; k < 7; k++)
					{
						datapoins2.push({ label: aux_dias[k], y: 0, color:"#808080" });
					}


				}

			}else{
				if(intervalo == "Ultimo Trimestre" || intervalo == "Último Año" || intervalo == "Trimestre Actual" || intervalo == "Año Actual")
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

	if(intervalo == "Ultimo Trimestre" || intervalo == "Trimestre Actual")
	{
  	var axisXTypeValue = "secondary";
	}else{
		var axisXTypeValue = "primary";
	}


	CanvasJS.addCultureInfo("es",
    {
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    }); 

	var chart = new CanvasJS.Chart("Emisiones_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Emisiones CO2",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "Emisiones CO2-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisY: {
			suffix: " kg CO2 eq",
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX:
		{
			labelFormatter: function(labelAxisX)
			{
				var	LabelX = ""+labelAxisX.label;
				if(intervalo == "Último Año" || intervalo == "Año Actual"){
					return LabelX.substr(0, LabelX.length - 6);
				}else if(intervalo == "Mes Actual" || intervalo == "Mes Anterior"){
					return LabelX.substr(0, LabelX.length - 8);
				}else if(intervalo == "Semana Actual" || intervalo == "Semana Anterior"){
					return LabelX.substr(0, LabelX.length - 12);
				}else{
					return labelAxisX.label;
				}
			},
			title: titleAxisX,
		//	title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
			titleFontColor: "#004165",
			titleFontSize: 12,
			lineColor: "#004165",
			labelFontColor: "#004165",
			interval: aux_interval,
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
			toolTipContent: aux_label+"{label}<br>{name}: {y} kg CO2 eq",
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Emisiones CO2",
			legendMarkerColor: "#004165",
			lineColor: "#004165",
			color: "#004165",
			dataPoints: datapoins1
		},
		{
			axisXType: axisXTypeValue,
			type: "spline",
			visible: true,
			showInLegend: true,
			toolTipContent: aux_label+"{label}<br>{name}: {y} kg CO2 eq",
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Emisiones CO2 (Anterior)",
			legendMarkerColor: "#808080",
			lineColor: "#808080",
			color: "#808080",
			dataPoints: datapoins2
		}]
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

	//console.log(dataURL);

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



			var canvas = $("#Emisiones_"+contador+" .canvasjs-chart-canvas").get(0);
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

		var htmlData = $(".export-pdf");
	/*donde pone el uno i < 1;  <--- es el numero de ".export-pdf que hay htmlData.length no funcciona correctamente por eso lo he puesto manual*/
		var arrIdx = [];
		for(var i = 0; i < 1; i++) {
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
