<script>


		// *****************************************
		// POTENCIA DEMANDADA - CONTRATADA - ÓPTIMA
		// *****************************************
		var arrayPeriodos = <?php echo json_encode($periodos2); ?>;
		var arrayDemandada = <?php echo json_encode($EAct); ?>;
		var arrayContratada = <?php echo json_encode($p_contratada); ?>;
		var arrayOptima = <?php echo json_encode($potencia_optima); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
		var intervalo = <?php echo json_encode($label_intervalo); ?>;
		var titulo = <?php echo json_encode($titulo); ?>;
		var date_from = <?php echo json_encode($date_from); ?>;
		var date_to = <?php echo json_encode($date_to); ?>;
		var aux_label = '';
		var aux_interval;
		var datapoins1 = [];
		var datapoins2 = [];
		var datapoins3 = [];

		for (var i = 0; i < arrayPeriodos.length; i++)
		{
			datapoins1.push({ label: arrayPeriodos[i], y: parseInt(arrayDemandada[i]), color:"#004165" },);
			datapoins2.push({ label: arrayPeriodos[i], y: parseInt(arrayContratada[i]), color:"#B9C9D0" },);
			datapoins3.push({ label: arrayPeriodos[i], y: parseInt(arrayOptima[i]['p_optima']), color: "#7D9AAA" },);
		}

		if(intervalo == "Ayer" || intervalo == "Hoy")
		{
			aux_label = "Hora: ";
			aux_interval = 1;
		}else{
			if(intervalo == "Mes Actual" || intervalo == "Mes Anterior")
			{
				aux_label = "Día: ";
				aux_interval = 1;
			}else{
				if(intervalo == "Semana Actual" || intervalo == "Semana Anterior")
				{
					aux_label = "Día: ";
				}else{
					if(intervalo == "Ultimo Trimestre" || intervalo == "Último Año")
					{
						aux_label = "Mes: ";
					}else{
						if(intervalo == "Personalizado" && date_to == date_from)
						{
							aux_label = "Hora: ";
							aux_interval = 1;
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
		CanvasJS.addCultureInfo("es",
	    {
	      decimalSeparator: ",",// Observe ToolTip Number Format
	      digitGroupSeparator: "."
	    });

		var chart1 = new CanvasJS.Chart("PotenciaDemanOptima_"+contador, {
			// animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			culture: "es",
			title:{
				text: "Potencia Demandada - Contratada - Óptima",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},
			exportFileName: "PotenciaDemandada-Contratada-Óptima-"+contador+"-"+date_from+"-"+date_to,
			exportEnabled: true,
			axisY: {
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				// valueFormatString:  "###0.##",
				suffix: " kW",
				tickColor: "#004165"
			},
			axisX: {
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries
			},
			data: [{
				type: "column",
				bevelEnabled: true,


		        indexLabelFontColor: "#FFF",
		        indexLabelBackgroundColor: "#004165",
				name: "Potencia Demandada",//Label del cursor
				legendText: "Potencia Demandada", // Label del legend
				legendMarkerColor: "#004165",
				toolTipContent: "{name}: {y}  kW",
				// yValueFormatString: "###0.## kW",
				showInLegend: true,

				dataPoints:datapoins1
			},
			{
				type: "column",
				name: "Potencia Contratada", // Label del cursor
				legendText: "Potencia Contratada", // Label del legend
				legendMarkerColor: "#B9C9D0",
				showInLegend: true,
				bevelEnabled: true,

		        indexLabelFontColor: "black",
		        indexLabelBackgroundColor: "#B9C9D0",
				toolTipContent: "{name}: {y}  kW",
				dataPoints: datapoins2
			},
			{
				type: "column",
				name: "Potencia Óptima", // Label del cursor
				legendText: "Potencia Óptima", // Label del legend
				legendMarkerColor: "#7D9AAA",
				toolTipContent: "{name}: {y}  kW",
				showInLegend: true,
				bevelEnabled: true,

		        indexLabelBackgroundColor: "#7D9AAA",
		        indexLabelFontColor: "black",
				dataPoints: datapoins3
			}]
		});
		chart1.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				// e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart1.render();
		}

</script>

	<script>
		// *****************************************
		// ENERGÍA CONSUMIDA - ACTIVA - REACTIVA
		// *****************************************
		var arrayPeriodos = <?php echo json_encode($periodos2); ?>;
		var arrayEnergia_Act = <?php echo json_encode($Energia_Act); ?>;
		var MaxEnergia_Act = <?php echo json_encode($energia_activa_max); ?>;

		var arrayEnergia_Reac_Cap = <?php echo json_encode($Energia_Reac_Cap); ?>;
		var arrayEnergia_Reac_Ind = <?php echo json_encode($Energia_Reac_Induc); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
		var datapoins1 = [];
		var datapoins2 = [];
		var datapoins3 = [];

		for (var i = 0; i < arrayPeriodos.length; i++)
		{
			datapoins1.push({ label: arrayPeriodos[i], y: parseInt(arrayEnergia_Act[i]), color:"#004165" },);
			datapoins2.push({ label: arrayPeriodos[i], y: parseInt(arrayEnergia_Reac_Ind[i]), color:"#B9C9D0" },);
			datapoins3.push({ label: arrayPeriodos[i], y: parseInt(arrayEnergia_Reac_Cap[i]), color: "#7D9AAA" },);
		}

		var aux1 = parseFloat(parseInt(MaxEnergia_Act).toExponential().split("e")[0]).toString();
		var aux2 = aux1.split(".");
		var aux3 = "0."+aux2[1];
		var maximo = 0;
		var div;

		for(var h = 0; h < arrayEnergia_Reac_Cap.length; h++ )
		{
			if(parseInt(arrayEnergia_Reac_Cap[h]) > MaxEnergia_Act)
				MaxEnergia_Act = arrayEnergia_Reac_Cap[h];
		}
		for(var h = 0; h < arrayEnergia_Reac_Ind.length; h++ )
		{
			if(parseInt(arrayEnergia_Reac_Ind[h]) > MaxEnergia_Act)
				MaxEnergia_Act = arrayEnergia_Reac_Ind[h];
		}
		//console.log('Energia maxima: '+MaxEnergia_Act);

		var aux1 = parseFloat(parseInt(MaxEnergia_Act).toExponential().split("e")[0]).toString();
		var aux2 = aux1.split(".");
		var aux3 = "0."+aux2[1];
		var maximo = 0;
		var div;
		if(parseFloat(aux3) < 0.5)
		{
			maximo = (parseInt(aux2[0])+0.5)*Math.pow(10,parseInt(parseInt(MaxEnergia_Act).toExponential().split("e")[1]));
			// console.log(maximo);
		}
		else{
			maximo = (parseInt(aux2[0])+1)*Math.pow(10,parseInt(parseInt(MaxEnergia_Act).toExponential().split("e")[1]));
			// console.log(parseInt(aux2[0])+1);
		}
		//console.log(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10);
		if(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10%4 == 0)
		{
			div = maximo/4;
		}else{
			if(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10%5 == 0){
				div = maximo/5;
			}

		}

		CanvasJS.addCultureInfo("es",
	    {
	      decimalSeparator: ",",// Observe ToolTip Number Format
	      digitGroupSeparator: "."
	    });

		var chart3 = new CanvasJS.Chart("ConsumidaActReac_"+contador, {
		// animationEnabled: true,
		culture: "es",
		theme: "light2", // "light1", "light2", "dark1", "dark2"
		title:{
			text: "Energía Consumida Activa - Reactiva",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "EnergíaConsumidaActiva-Reactiva-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisY: {
			suffix: " kWh",
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			viewportMaximum: maximo,
			interval: div,
			// viewportMinimum: 1000,
			tickColor: "#004165"
		},
		axisX: {
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165"
			},
		axisY2: {
			suffix: " kVArh",
			titleFontColor: "#004165",
			lineColor: "#004165",
			viewportMaximum: maximo,
			interval: div,
			// viewportMinimum: 1000,
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor:"pointer",
			itemclick: toggleDataSeries
		},
		data: [{
			type: "column",
			bevelEnabled: true,
			name: "Energía Activa",//Label del cursor
			legendText: "Energía Activa", // Label del legend
			legendMarkerColor: "#004165",
			toolTipContent: "{name}: {y}  kWh",
			// yValueFormatString: "###0.## kWh",
			showInLegend: true,
			dataPoints:datapoins1
		},
		{
			type: "column",
			name: "Energía Reactiva Ind", // Label del cursor
			bevelEnabled: true,
			legendText: "Energía Reactiva Ind", // Label del legend
			legendMarkerColor: "#B9C9D0",
			toolTipContent: "{name}: {y}  kVArh",
			// yValueFormatString: "###0.## kVArh",
			axisYType: "secondary",
			showInLegend: true,
			dataPoints:datapoins2
		},
		{
			type: "column",
			name: "Energía Reactiva Cap", // Label del cursor
			legendText: "Energía Reactiva Cap", // Label del legend
			toolTipContent: "{name}: {y}  kVArh",
			legendMarkerColor: "#7D9AAA",
			bevelEnabled: true,
			axisYType: "secondary",
			showInLegend: true,
			dataPoints:datapoins3
		}]
		});
		chart3.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart3.render();
		}
	</script>



	<script>
		// *****************************************
			// COSTE TÉRMINO POTENCIA
			// *****************************************

			var arrayPeriodos = <?php echo json_encode($periodos_coste); ?>;

			var arrayCostePotencia = <?php echo json_encode($coste_potencia); ?>;
			var total = <?php echo json_encode(number_format($coste_potencia[$peri],'2',',','.')); ?>;
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
			var datapoins1 = [];

			for (var i = 0; i < arrayPeriodos.length; i++)
			{
				if(i%3 == 0 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCostePotencia[i]), label: arrayPeriodos[i], color: "#004165" },);
				}
				if(i%3 == 1 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCostePotencia[i]),  label: arrayPeriodos[i], color: "#B9C9D0" },);
				}
				if(i%3 == 2 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCostePotencia[i]),  label: arrayPeriodos[i], color: "#7D9AAA" },);
				}
				if(i == arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCostePotencia[i]),  label: arrayPeriodos[i], color: "#7F7F7F" },);
				}

				// { y: parseInt(arrayCostePotencia[3]),  label: arrayPeriodos[3], color: "#004165" },
				// { y: parseInt(arrayCostePotencia[4]),  label: arrayPeriodos[4], color: "#B9C9D0" },
				// { y: parseInt(arrayCostePotencia[5]), label: arrayPeriodos[5], color: "#7D9AAA" },
				// { y: parseInt(arrayCostePotencia[6]),  label: arrayPeriodos[6], color: "#7F7F7F" }
			}
			//console.log('EPA',datapoins1);
			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });

			var chart2 = new CanvasJS.Chart("CostePotencia_"+contador, {
				// animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				culture: "es",
				title:{
					text: "Coste Término Potencia",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "CosteTérminoPotencia-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					suffix: " €",
					// valueFormatString:  "###0.##"
				},
				axisX: {
					title: 'Total: '+total+' €',
					titleFontSize: 12,
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				data: [{
					type: "column",
					showInLegend: true,
					legendMarkerColor: "waith",
					toolTipContent: "{label}: {y} €",
					bevelEnabled: true,
					// yValueFormatString: "###0.## €",
					legendText: " ",
					dataPoints: datapoins1
				}]
			});
			chart2.render();
	</script>

	<!-- Hecho por Cecilio -->
	<script>
			var arrayCosteEnergia = <?php echo json_encode($coste_termino_energia); ?>;
			var arrayPeriodos = <?php echo json_encode($periodos_coste); ?>;
			var total = <?php echo json_encode(number_format($coste_termino_energia[$peri],'2',',','.')); ?>;
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
			var datapoins1 = [];
			for (var i = 0; i < arrayPeriodos.length; i++)
			{
				if(i%3 == 0 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCosteEnergia[i]), label: arrayPeriodos[i], color: "#004165" },);
				}
				if(i%3 == 1 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCosteEnergia[i]),  label: arrayPeriodos[i], color: "#B9C9D0" },);
				}
				if(i%3 == 2 && i != arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCosteEnergia[i]),  label: arrayPeriodos[i], color: "#7D9AAA" },);
				}
				if(i == arrayPeriodos.length-1)
				{
					datapoins1.push({ y: parseInt(arrayCosteEnergia[i]),  label: arrayPeriodos[i], color: "#7F7F7F" },);
				}
			}

			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });

			var chart4 = new CanvasJS.Chart("CosteTerminoEnergia_"+contador, {
				// animationEnabled: true,
				culture: "es",
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				title:{
					text: "Coste Término Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "CosteTérminoEnergía-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					// valueFormatString:  "###0.##",
					suffix: " €"
				},
				axisX: {
					title: 'Total: '+total+' €',
					titleFontSize: 12,
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"

				},
				data: [{
					type: "column",
					showInLegend: true,
					legendMarkerColor: "waith",
					toolTipContent: "{label}: {y} €",
					bevelEnabled: true,
					// yValueFormatString: "###0.## €",
					legendText: " ",
					dataPoints: datapoins1
				}]
			});
			chart4.render();
	</script>




	<script>
		// *****************************************
			// VENTA ENERGIA
			// *****************************************

			var arrayVenta = <?php echo json_encode($db_Venta_Energia); ?>;
			var totalVentas = <?php echo json_encode(number_format($total_ventas,'2',',','.')); ?>;
			var totalVentas2 = <?php echo json_encode($total_ventas); ?>;
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;

			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });

			var chart2 = new CanvasJS.Chart("VentasEnergia_"+contador, {
				// animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				culture: "es",
				title:{
					text: "Venta Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "VentaEnergía-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					suffix: " €",
					// valueFormatString:  "###0.##"
				},
				axisX: {
					title: 'Total: '+totalVentas+' €',
					titleFontSize: 12,
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				data: [{
					type: "column",
					showInLegend: true,
					legendMarkerColor: "waith",
					toolTipContent: "{label}: {y} €",
					bevelEnabled: true,
					// yValueFormatString: "###0.## €",
					legendText: " ",
					dataPoints: [
						{ y: parseInt(arrayVenta[0]["ventaP1"]*1), label: arrayPeriodos[0], color: "#004165" },
						{ y: parseInt(arrayVenta[0]["ventaP2"]*1),  label: arrayPeriodos[1], color: "#B9C9D0" },
						{ y: parseInt(arrayVenta[0]["ventaP3"]*1),  label: arrayPeriodos[2], color: "#7D9AAA" },
						{ y: parseInt(arrayVenta[0]["ventaP4"]*1),  label: arrayPeriodos[3], color: "#004165" },
						{ y: parseInt(arrayVenta[0]["ventaP5"]*1),  label: arrayPeriodos[4], color: "#B9C9D0" },
						{ y: parseInt(arrayVenta[0]["ventaP6"]*1), label: arrayPeriodos[5], color: "#7D9AAA" },
						{ y: parseInt(totalVentas2),  label: "Total", color: "#7F7F7F" }
					]
				}]
			});
			chart2.render();
	</script>


<script>
		// *****************************************
			// VENTA ENERGIA SUB-TIPO 1
			// *****************************************	
	   
	  
			var arrayVenta = <?php echo json_encode($db_Venta_Energia); ?>;
			var arrayVentaCosto = <?php echo json_encode($db_Venta_Costo_Energia); ?>;
			var totalVentas = <?php echo json_encode(number_format($total_ventas,'2',',','.')); ?>;
			var totalVentas2 = <?php echo json_encode($total_ventas); ?>;
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
/***********************************************GENERATE ARRAY*****************************/			
	var arrayDataFiltered = [];
	if(arrayVenta && arrayVenta.length > 0)
		{
	/**********P1***************/	
	
	   				if(arrayVenta[0]["periodoP"] != "P1")
				{
							
					
						arrayDataFiltered.push({
									y: parseInt(0), 
									label: arrayPeriodos[0],
									color: "#004165"				
								})	

				}else{
					arrayDataFiltered.push({
								y: parseInt(arrayVenta[0]["energySold"]*arrayVentaCosto[0]["totalPrice"]), 
								label: arrayPeriodos[0],
								color: "#004165"				
							})	

				}		
	   
		
	
	
	/**********P2***************/
	
	
	
	   		if(arrayVenta[0]["periodoP"] == "P2")
				{						
					
						arrayDataFiltered.splice(1,0,{
									y: parseInt(arrayVenta[0]["energySold"]*arrayVentaCosto[1]["totalPrice"]), 
								    label: arrayPeriodos[1],
								    color: "#B9C9D0"		
								})	

				}else if(arrayVenta[1]["periodoP"] == "P2"){				
					
					arrayDataFiltered.splice(1,0,{
									y: parseInt(arrayVenta[1]["energySold"]*arrayVentaCosto[1]["totalPrice"]), 
								    label: arrayPeriodos[1],
								    color: "#B9C9D0"		
								})	

				}else if(arrayVenta[2]["periodoP"] == "P2"){				
					
					arrayDataFiltered.splice(1,0,{
									y: parseInt(arrayVenta[2]["energySold"]*arrayVentaCosto[1]["totalPrice"]), 
								    label: arrayPeriodos[1],
								    color: "#B9C9D0"		
								})	

				}else{				
					
					arrayDataFiltered.splice(1,0,{
									y: parseInt(0), 
									label: arrayPeriodos[1],
									color: "#B9C9D0"				
								})	

				}
			
		
	
	
	/**********P3***************/	

	
	
	
	   		
	   		if(arrayVenta[0]["periodoP"] == "P3")
				{						
					
						arrayDataFiltered.splice(2,0,{
									y: parseInt(arrayVenta[0]["energySold"]*arrayVentaCosto[2]["totalPrice"]), 
								    label: arrayPeriodos[2],
								    color: "#7D9AAA"		
								})	

				}else if(arrayVenta[1]["periodoP"] == "P3"){				
					
					arrayDataFiltered.splice(2,0,{
									y: parseInt(arrayVenta[1]["energySold"]*arrayVentaCosto[2]["totalPrice"]), 
								    label: arrayPeriodos[2],
								    color: "#7D9AAA"		
								})	

				}else if(arrayVenta[2]["periodoP"] == "P3"){				
					
					arrayDataFiltered.splice(2,0,{
									y: parseInt(arrayVenta[2]["energySold"]*arrayVentaCosto[2]["totalPrice"]), 
								    label: arrayPeriodos[2],
								    color: "#7D9AAA"		
								})	

				}else{				
					
					arrayDataFiltered.splice(2,0,{
									y: parseInt(0), 
									label: arrayPeriodos[2],
									color: "#7D9AAA"				
								})	

				}	
			
	}else{
		
		
		
					arrayDataFiltered.push({
									y: parseInt(0), 
									label: arrayPeriodos[0],
									color: "#004165"				
								})	
		
					arrayDataFiltered.splice(1,0,{
									y: parseInt(0), 
									label: arrayPeriodos[1],
									color: "#B9C9D0"				
								})	
		
					arrayDataFiltered.splice(2,0,{
									y: parseInt(0), 
									label: arrayPeriodos[2],
									color: "#7D9AAA"				
								})	
		
		}
	
   	
/**********Total Ventas***************/	
	
	if(totalVentas2 > 0)
	{
	   			arrayDataFiltered.push({
					        y: parseInt(totalVentas2),
							label: "Total",
							color: "#7F7F7F" 			
				})			
	   
    }else{
		arrayDataFiltered.push({
					        y: parseInt(0),
							label: "Total",
							color: "#7F7F7F" 				
				})	
		
	}
	
			
			
/***********************************************GENERATE ARRAY*****************************/
			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });

			var chartEnergyType2 = new CanvasJS.Chart("VentasEnergiaType2_"+contador, {
				// animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				culture: "es",
				title:{
					text: "Venta Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "VentaEnergía-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					suffix: " €",
					// valueFormatString:  "###0.##"
				},
				axisX: {
					title: 'Total: '+totalVentas+' €',
					titleFontSize: 12,
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				data: [{
					type: "column",
					showInLegend: true,
					legendMarkerColor: "waith",
					toolTipContent: "{label}: {y} €",
					bevelEnabled: true,
					// yValueFormatString: "###0.## €",
					legendText: " ",
					dataPoints : arrayDataFiltered
					/*dataPoints: [
						{
							y: parseInt(arrayVenta[0]["energySold"]*arrayVentaCosto[0]["totalPrice"]), 
						    label: arrayPeriodos[0],
						    color: "#004165" 
						},
						
						{
						    y: parseInt(arrayVenta[1]["energySold"]*arrayVentaCosto[1]["totalPrice"]),
						    label: arrayPeriodos[1], 
							color: "#B9C9D0" 
						},
						
						{
						    y: parseInt(arrayVenta[2]["energySold"]*arrayVentaCosto[2]["totalPrice"]),
							label: arrayPeriodos[2],
							color: "#7D9AAA" 
						},						
						{
							y: parseInt(totalVentas2),
							label: "Total",
							color: "#7F7F7F" 
						}
					]*/
				}]
			});
			chartEnergyType2.render();
	 
	</script>

		<script>
		// *****************************************
			// GENERACION ENERGIA
			// *****************************************


			function toggleDataSeries(e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				}
				else {
					e.dataSeries.visible = true;
				}
				chart2.render();
			}

			var arrayGeneracion = <?php echo json_encode($generacion); ?>;
		//	console.log(arrayGeneracion);
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
			var aux_cont = <?php echo json_encode(count($generacion)); ?>;
			//var max_activa2 = arrayGeneracion['activa'];
		  var max_activa2 = [];
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ activa }) => activa))));
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ inductiva }) => inductiva))));
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ capacitiva }) => capacitiva))));
			max_activa2 = Math.max(...max_activa2);
			//max = Math.max(...peaks.map(({ value }) => value))
			//console.log('max_activa: '+max_activa2);

			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });



				var aux12 = parseFloat(parseInt(max_activa2).toExponential().split("e")[0]).toString();
				var aux22 = aux12.split(".");
				var aux32 = "0."+aux22[1];
				var maximo2 = 0;
				var div2;


				if(parseFloat(aux32) < 0.5)
				{
					maximo2 = (parseInt(aux22[0])+0.5)*Math.pow(10,parseInt(parseInt(max_activa2).toExponential().split("e")[1]));
					// console.log(maximo);
				}
				else{
					maximo2 = (parseInt(aux22[0])+1)*Math.pow(10,parseInt(parseInt(max_activa2).toExponential().split("e")[1]));
					// console.log(parseInt(aux2[0])+1);
				}

				if(parseFloat((maximo2.toExponential(1)).toString().split("e")[0])*10%4 == 0)
				{
					div2 = maximo2/4;
				}else{
					if(parseFloat((maximo2.toExponential(1)).toString().split("e")[0])*10%5 == 0){
						div2 = maximo2/5;
					}

				}


			var chart2 = new CanvasJS.Chart("Generacion_"+contador, {
				// animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				culture: "es",
				title:{
					text: "Generación Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "GeneraciónEnergía-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					suffix: " kWh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					viewportMaximum: maximo2,
					interval: div2,
					// viewportMinimum: 1000,
					labelFontColor: "#004165",
					tickColor: "#004165"
					// valueFormatString:  "###0.##"
				},
				axisX: {
					// title: 'Total: '+totalVentas+' kWh',
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165",
					interval: 1
				},
				axisY2: {
					suffix: " kVArh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					viewportMaximum: maximo2,
					interval: div2,
					// viewportMinimum: 1000,
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				toolTip: {
					shared: true
				},
				legend: {
					cursor:"pointer",
					itemclick: toggleDataSeries
				},
				data: [{
					type: "column",
					cursor: "zoom-in",
					bevelEnabled: true,
					name: "Energía Activa",//Label del cursor
					legendText: "Energía Activa", // Label del legend
					legendMarkerColor: "#004165",
					toolTipContent: "{name}: {y} kWh",
					showInLegend: true,
					dataPoints: [
						{ y: parseInt(arrayGeneracion[0]["activa"]), label: "P1", color: "#004165"},
						{ y: parseInt(arrayGeneracion[1]["activa"]),  label: "P2", color: "#004165"},
						{ y: parseInt(arrayGeneracion[2]["activa"]),  label: "P3", color: "#004165"},
						{ y: parseInt(arrayGeneracion[3]["activa"]),  label: "P4", color: "#004165"},
						{ y: parseInt(arrayGeneracion[4]["activa"]),  label: "P5", color: "#004165"},
						{ y: parseInt(arrayGeneracion[5]["activa"]), label: "P6", color: "#004165"}
					]},
					{
						type: "column",
    				name: "Energía Reactiva Ind", // Label del cursor
    				legendText: "Energía Reactiva Ind", // Label del legend
    				legendMarkerColor: "#B9C9D0",
    				toolTipContent: "{name}: {y} kVArh",
    				bevelEnabled: true,
    				axisYType: "secondary",
    				showInLegend: true,
						dataPoints: [
							{ y: parseInt(arrayGeneracion[0]["inductiva"]), label: "P1", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[1]["inductiva"]),  label: "P2", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[2]["inductiva"]),  label: "P3", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[3]["inductiva"]),  label: "P4", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[4]["inductiva"]),  label: "P5", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[5]["inductiva"]), label: "P6", color: "#B9C9D0"}
					]},
					{
						type: "column",
    				name: "Energía Reactiva Cap", // Label del cursor
    				legendText: "Energía Reactiva Cap", // Label del legend
    				toolTipContent: "{name}: {y} kVArh",
    				bevelEnabled: true,
    				legendMarkerColor: "#7D9AAA",
    				axisYType: "secondary",
    				showInLegend: true,
						dataPoints: [
							{ y: parseInt(arrayGeneracion[0]["capacitiva"]), label: "P1", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[1]["capacitiva"]),  label: "P2", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[2]["capacitiva"]),  label: "P3", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[3]["capacitiva"]),  label: "P4", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[4]["capacitiva"]),  label: "P5", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[5]["capacitiva"]), label: "P6", color: "#7D9AAA"}
					]},
				]
			});
			chart2.render();
	</script>



	<script>
		// *****************************************
			// GENERACION ENERGIA SUB-TIPO 1
			// *****************************************


			function toggleDataSeries(e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				}
				else {
					e.dataSeries.visible = true;
				}
				chartType2.render();
			}

			var arrayGeneracion = <?php echo json_encode($generacion); ?>;
		//	console.log(arrayGeneracion);
			var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
			var aux_cont = <?php echo json_encode(count($generacion)); ?>;
			//var max_activa2 = arrayGeneracion['activa'];
		  var max_activa2 = [];
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ activa }) => activa))));
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ inductiva }) => inductiva))));
			max_activa2.push(parseInt(Math.max(...arrayGeneracion.map(({ capacitiva }) => capacitiva))));
			max_activa2 = Math.max(...max_activa2);
			//max = Math.max(...peaks.map(({ value }) => value))
			//console.log('max_activa: '+max_activa2);

			CanvasJS.addCultureInfo("es",
		    {
		      decimalSeparator: ",",// Observe ToolTip Number Format
		      digitGroupSeparator: "."
		    });



				var aux12 = parseFloat(parseInt(max_activa2).toExponential().split("e")[0]).toString();
				var aux22 = aux12.split(".");
				var aux32 = "0."+aux22[1];
				var maximo2 = 0;
				var div2;


				if(parseFloat(aux32) < 0.5)
				{
					maximo2 = (parseInt(aux22[0])+0.5)*Math.pow(10,parseInt(parseInt(max_activa2).toExponential().split("e")[1]));
					// console.log(maximo);
				}
				else{
					maximo2 = (parseInt(aux22[0])+1)*Math.pow(10,parseInt(parseInt(max_activa2).toExponential().split("e")[1]));
					// console.log(parseInt(aux2[0])+1);
				}

				if(parseFloat((maximo2.toExponential(1)).toString().split("e")[0])*10%4 == 0)
				{
					div2 = maximo2/4;
				}else{
					if(parseFloat((maximo2.toExponential(1)).toString().split("e")[0])*10%5 == 0){
						div2 = maximo2/5;
					}

				}


			var chartType2 = new CanvasJS.Chart("GeneracionType2_"+contador, {
				// animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				culture: "es",
				title:{
					text: "Generación Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				exportFileName: "GeneraciónEnergía-"+contador+"-"+date_from+"-"+date_to,
				exportEnabled: true,
				axisY: {
					suffix: " kWh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					viewportMaximum: maximo2,
					interval: div2,
					// viewportMinimum: 1000,
					labelFontColor: "#004165",
					tickColor: "#004165"
					// valueFormatString:  "###0.##"
				},
				axisX: {
					// title: 'Total: '+totalVentas+' kWh',
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165",
					interval: 1
				},
				axisY2: {
					suffix: " kVArh",
					titleFontColor: "#004165",
					lineColor: "#004165",
					viewportMaximum: maximo2,
					interval: div2,
					// viewportMinimum: 1000,
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				toolTip: {
					shared: true
				},
				legend: {
					cursor:"pointer",
					itemclick: toggleDataSeries
				},
				data: [{
					type: "column",
					cursor: "zoom-in",
					bevelEnabled: true,
					name: "Energía Activa",//Label del cursor
					legendText: "Energía Activa", // Label del legend
					legendMarkerColor: "#004165",
					toolTipContent: "{name}: {y} kWh",
					showInLegend: true,
					dataPoints: [
						{ y: parseInt(arrayGeneracion[0]["activa"]), label: "P1", color: "#004165"},
						{ y: parseInt(arrayGeneracion[1]["activa"]),  label: "P2", color: "#004165"},
						{ y: parseInt(arrayGeneracion[2]["activa"]),  label: "P3", color: "#004165"}						
					]},
					{
						type: "column",
    				name: "Energía Reactiva Ind", // Label del cursor
    				legendText: "Energía Reactiva Ind", // Label del legend
    				legendMarkerColor: "#B9C9D0",
    				toolTipContent: "{name}: {y} kVArh",
    				bevelEnabled: true,
    				axisYType: "secondary",
    				showInLegend: true,
						dataPoints: [
							{ y: parseInt(arrayGeneracion[0]["inductiva"]), label: "P1", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[1]["inductiva"]),  label: "P2", color: "#B9C9D0"},
							{ y: parseInt(arrayGeneracion[2]["inductiva"]),  label: "P3", color: "#B9C9D0"}							
					]},
					{
						type: "column",
    				name: "Energía Reactiva Cap", // Label del cursor
    				legendText: "Energía Reactiva Cap", // Label del legend
    				toolTipContent: "{name}: {y} kVArh",
    				bevelEnabled: true,
    				legendMarkerColor: "#7D9AAA",
    				axisYType: "secondary",
    				showInLegend: true,
						dataPoints: [
							{ y: parseInt(arrayGeneracion[0]["capacitiva"]), label: "P1", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[1]["capacitiva"]),  label: "P2", color: "#7D9AAA"},
							{ y: parseInt(arrayGeneracion[2]["capacitiva"]),  label: "P3", color: "#7D9AAA"}							
					]},
				]
			});
			chartType2.render();
	</script>
