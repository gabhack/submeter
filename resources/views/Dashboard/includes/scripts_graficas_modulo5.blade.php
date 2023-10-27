	<script>
		// *****************************************
		// COSTE TÉRMINO POTENCIA
		// *****************************************

			var arrayVar1 = <?php echo json_encode($precio_energia); ?>;//correponde a precio actual
			var arrayVar2 = <?php echo json_encode($precio_potencia); ?>;//corresponde a precio propuesto
			var contador = <?php echo json_encode($user->_count[0]->count_label) ?>;
			var datapoins1 = [];
			var datapoins2 = [];
			var totalE = 0;
			var totalP = 0;

			for (var i = 0; i < arrayVar1.length; i++) {
				datapoins1.push({ label: arrayVar1[i]['eje'], y: parseFloat(arrayVar1[i][
					'precio_energia']), color:"#004165" });
				datapoins2.push({ label: arrayVar2[i]['eje'], y: parseFloat(arrayVar2[i][
					'precio_potencia']), color:"#B9C9D0" });
				totalE = parseFloat(arrayVar1[i]['precio_energia']) + totalE;
				totalP = parseFloat(arrayVar2[i]['precio_potencia']) + totalP;
			}
			datapoins1.push({ label: "TOTAL", y: totalE, color:"#004165" });
			datapoins2.push({ label: "TOTAL", y: totalP, color:"#B9C9D0" });			
			console.log(arrayVar1);
			console.log(datapoins1);
			
			var chart1 = new CanvasJS.Chart("CosteEnergia_"+contador, {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			title:{
				text: "Coste Término Energía",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				valueFormatString:  "###0.########",
				suffix: "kW",
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Energía",//Label del cursor
				legendText: "Coste Energía", // Label del legend
				legendMarkerColor: "#004165",
				showInLegend: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Potencia", // Label del cursor
				legendText: "Coste Potencia", // Label del legend
				legendMarkerColor: "#B9C9D0",
				showInLegend: true,
				dataPoints:datapoins2
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
			var arrayVar1 = <?php echo json_encode($precio_energia); ?>;//correponde a precio actual
			var arrayVar2 = <?php echo json_encode($precio_potencia); ?>;//corresponde a precio propuesto
			var contador = <?php echo json_encode($user->_count[0]->count_label) ?>;
			var datapoins1 = [];
			var datapoins2 = [];
			var totalE = 0;
			var totalP = 0;

			for (var i = 0; i < arrayVar1.length; i++) {
				datapoins1.push({ label: arrayVar1[i]['eje'], y: parseFloat(arrayVar1[i][
					'precio_energia']), color:"#004165" });
				datapoins2.push({ label: arrayVar2[i]['eje'], y: parseFloat(arrayVar2[i][
					'precio_potencia']), color:"#B9C9D0" });
				totalE = parseFloat(arrayVar1[i]['precio_energia']) + totalE;
				totalP = parseFloat(arrayVar2[i]['precio_potencia']) + totalP;
			}			
			console.log(arrayVar1);
			console.log(datapoins1);
			datapoins1.push({ label: "TOTAL", y: totalE, color:"#004165" });
			datapoins2.push({ label: "TOTAL", y: totalP, color:"#B9C9D0" });
			
			var chart2 = new CanvasJS.Chart("CostePotencia_"+contador, {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			title:{
				text: "Coste Término Energía",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				valueFormatString:  "###0.########",
				suffix: "kW",
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Energía",//Label del cursor
				legendText: "Coste Energía", // Label del legend
				legendMarkerColor: "#004165",
				showInLegend: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Potencia", // Label del cursor
				legendText: "Coste Potencia", // Label del legend
				legendMarkerColor: "#B9C9D0",
				showInLegend: true,
				dataPoints:datapoins2
			}]
		});
		chart2.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				// e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart2.render();
		}
	</script>
	
	<script>
		function ejecutar_consulta(label_contador){
			
			var arrayVar1 = <?php echo json_encode($precio_energia); ?>;//correponde a precio actual
			var arrayVar2 = <?php echo json_encode($precio_potencia); ?>;//corresponde a precio propuesto
			var contador = <?php echo json_encode($user->_count[0]->count_label) ?>;
			var datapoins1 = [];
			var datapoins2 = [];
			var totalE = 0;
			var totalP = 0;
			
			// *****************************************
			// COSTE TÉRMINO POTENCIA
			// *****************************************

			for (var i = 0; i < arrayVar1.length; i++) {
				datapoins1.push({ label: arrayVar1[i]['eje'], y: parseFloat(arrayVar1[i][
					'precio_energia']), color:"#004165" });
				datapoins2.push({ label: arrayVar2[i]['eje'], y: parseFloat(arrayVar2[i][
					'precio_potencia']), color:"#B9C9D0" });
				totalE = parseFloat(arrayVar1[i]['precio_energia']) + totalE;
				totalP = parseFloat(arrayVar2[i]['precio_potencia']) + totalP;
			}
			datapoins1.push({ label: "TOTAL", y: totalE, color:"#004165" });
			datapoins2.push({ label: "TOTAL", y: totalP, color:"#B9C9D0" });		
			console.log(arrayVar1);
			console.log(datapoins1);
			
			var chart1 = new CanvasJS.Chart("CosteEnergia_"+contador, {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			title:{
				text: "Coste Término Energía",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				valueFormatString:  "###0.########",
				suffix: "kW",
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Energía",//Label del cursor
				legendText: "Coste Energía", // Label del legend
				legendMarkerColor: "#004165",
				showInLegend: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Potencia", // Label del cursor
				legendText: "Coste Potencia", // Label del legend
				legendMarkerColor: "#B9C9D0",
				showInLegend: true,
				dataPoints:datapoins2
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

		var chart2 = new CanvasJS.Chart("CostePotencia_"+contador, {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			title:{
				text: "Coste Término Energía",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				valueFormatString:  "###0.########",
				suffix: "kW",
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Energía",//Label del cursor
				legendText: "Coste Energía", // Label del legend
				legendMarkerColor: "#004165",
				showInLegend: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Potencia", // Label del cursor
				legendText: "Coste Potencia", // Label del legend
				legendMarkerColor: "#B9C9D0",
				showInLegend: true,
				dataPoints:datapoins2
			}]
		});
		chart2.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				// e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart2.render();
		}			

		// ******************************************
		// COSTE TÉRMINO ENERGÍA
		// ******************************************
		var chart4 = new CanvasJS.Chart("CostePotencia_"+contador, {
				animationEnabled: true,
				theme: "light2", // "light1", "light2", "dark1", "dark2"
				title:{
					text: "Coste Término Energía",
					fontSize: 18,
					margin: 50,
					fontColor: "#004165"
				},
				axisY: {
					suffix: "€",
					valueFormatString:  "###0.##"
				},
				data: [{        
					type: "column",  
					showInLegend: true, 
					legendMarkerColor: "waith",
					legendText: " ",
					dataPoints: datapoins1
				},
				{        
					type: "column",  
					showInLegend: true, 
					legendMarkerColor: "waith",
					legendText: " ",
					dataPoints: datapoins2
				}
				]
			});
			chart4.render();
		
		}		
	</script>