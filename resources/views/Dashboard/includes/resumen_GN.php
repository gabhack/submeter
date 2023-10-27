<script>	
	var arrayConsumoNm3 = <?php echo json_encode($consumo_GN_Nm3); ?>;
	var arrayConsumokWh = <?php echo json_encode($consumo_GN_kWh); ?>;
	var intervalo = <?php echo json_encode($label_intervalo); ?>;
	var date_from = <?php echo json_encode($date_from); ?>;
	var date_to = <?php echo json_encode($date_to); ?>;
	var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
	var datapoins1 = [];
	var datapoins2 = [];
	var total1 = <?php echo json_encode(number_format($total1,0,',','.')); ?>;
	var total2 = <?php echo json_encode(number_format($total2,0,',','.')); ?>;
	var empresa = "Empresa: "+<?php echo json_encode($user->name); ?>;
	var ubicacion = "Ubicación: "+<?php echo json_encode($direccion); ?>;	
	
	for (var i = 0; i < arrayConsumoNm3.length; i++) {
		datapoins1.push({ label: arrayConsumoNm3[i]['eje'], y: parseFloat(arrayConsumoNm3[i]['consumo']), color:"#004165" });
	}
	for (var i = 0; i < arrayConsumokWh.length; i++) {
		datapoins2.push({ label: arrayConsumokWh[i]['eje'], y: parseFloat(arrayConsumokWh[i]['consumo']), color:"#004165" });
	}

	CanvasJS.addCultureInfo("es", 
    {      
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    });
    var interval_X;
    
    if(intervalo == 'Ayer' || intervalo == 'Hoy' || (intervalo == 'Personalizado' && date_from == date_to))
    {
    	if($(window).width() > 1580)
			interval_X = 2;
		if ($(window).width() < 1580 && $(window).width() > 1330)
			interval_X = 3;
	    if ($(window).width() < 1330 && $(window).width() > 1160) // interval = 4
	    	interval_X = 4;
	    if ($(window).width() < 1160 && $(window).width() > 1030) // interval = 5
	    	interval_X = 5;
	    if ($(window).width() < 1030 && $(window).width() > 980) // interval = 6
	    	interval_X = 6;
	    if ($(window).width() < 980 && $(window).width() > 840) // interval = 7
	    	interval_X = 7;
	    if ($(window).width() < 840 && $(window).width() > 768) // interval = 8
	    	interval_X = 8;
	    if ($(window).width() < 768 && $(window).width()  > 750) // interval = 5
	    	interval_X = 5;
	    if ($(window).width() < 750 && $(window).width() > 680)  // interval = 6
	    	interval_X = 6;
	    if ($(window).width() < 680 && $(window).width() > 620) // interval = 7
	    	interval_X = 7;
	    if ($(window).width() < 620 && $(window).width() > 570) // interval = 8
	    	interval_X = 8;
	    if ($(window).width() < 570 && $(window).width() > 510) // interval = 9
	    	interval_X = 9;
	    if ($(window).width() < 510 && $(window).width() > 460)  // interval = 10
	    	interval_X = 10;
	    if ($(window).width() < 460 && $(window).width() > 440)  // interval = 11
	    	interval_X = 11;
	    if ($(window).width() < 440 && $(window).width() > 425) // interval = 12
	    	interval_X = 12;
	    if ($(window).width() < 425)  // interval = 15
	    	interval_X = 15;

	    $(window).resize(function() {
			if($(window).width() > 1580)  // interval = 2
			{
				interval_X = 2;
				var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
			}
			if ($(window).width() < 1580 && $(window).width() > 1330) // interval = 3
		    {
		    	interval_X = 3;

		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
			if ($(window).width() < 1330 && $(window).width() > 1160) // interval = 4
		    {
		    	interval_X = 4;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 1160 && $(window).width() > 1030) // interval = 5
		    {
		    	interval_X = 5;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 1030 && $(window).width() > 980) // interval = 6
		    {
		    	interval_X = 6;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 980 && $(window).width() > 840) // interval = 7
		    {
		    	interval_X = 7;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 840 && $(window).width() > 768) // interval = 8
		    {
		    	interval_X = 8;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 768 && $(window).width()  > 750) // interval = 5
		    {
		    	interval_X = 5;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 750 && $(window).width() > 680)  // interval = 6
		    {
		    	interval_X = 6;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 680 && $(window).width() > 620) // interval = 7
		    {
		    	interval_X = 7;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 620 && $(window).width() > 570) // interval = 8
		    {
		    	interval_X = 8;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 570 && $(window).width() > 510) // interval = 9
		    {
		    	interval_X = 9;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 510 && $(window).width() > 460)  // interval = 10
		    {
		    	interval_X = 10;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 460 && $(window).width() > 440)  // interval = 11
		    {
		    	interval_X = 11;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 440 && $(window).width() > 425) // interval = 12
		    {
		    	interval_X = 12;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		    if ($(window).width() < 425)  // interval = 15
		    {
		    	interval_X = 15;
		    	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
					theme: "light2",
					culture: "es",

					title:{
						text: "Consumo GN (Nm3)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " Nm3",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total2+" Nm3",
						titleFontColor: "#004165",
						titleFontSize: 12,
						lineColor: "#004165",
						interval: interval_X,
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
						toolTipContent: "{name}: {y} Nm3",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo Nm3",
						legendMarkerColor: "#B9C9D0",
						lineColor: "#B9C9D0",
						color: "#B9C9D0",
						dataPoints: datapoins1		
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
		    }
		});
    }

	var chart = new CanvasJS.Chart("Consumo_Nm3_"+contador, {
		theme: "light2",
		culture: "es",

		title:{
			text: "Consumo GN (Nm3)",
			fontSize: 18,				
			margin: 50,
			fontColor: "#004165"
		},
		exportEnabled: true,
		axisY: {
			suffix: " Nm3",
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			title: "CONSUMO GN "+total2+" Nm3",
			titleFontColor: "#004165",
			titleFontSize: 12,
			lineColor: "#004165",
			interval: interval_X,
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
			toolTipContent: "{name}: {y} Nm3",
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Consumo Nm3",
			legendMarkerColor: "#B9C9D0",
			lineColor: "#B9C9D0",
			color: "#B9C9D0",
			dataPoints: datapoins1		
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

	$(window).resize(function() {
			if($(window).width() > 1580)  // interval = 2
			{
				interval_X = 2;
				var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Consumo GN (kWh)",
			fontSize: 18,				
			margin: 50,
			fontColor: "#004165"
		},
		exportEnabled: true,
		axisY: {
			suffix: " kWh",
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			title: "CONSUMO GN "+total1+" kWh",
			interval: interval_X,
			titleFontColor: "#004165",
			titleFontSize: 12,
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
			type: "column",
			visible: true,
			showInLegend: true,
			toolTipContent: "{name}: {y} kWh",
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Consumo kWh",
			legendMarkerColor: "#004165",
			lineColor: "#004165",
			color: "#004165",
			dataPoints: datapoins2		
		}]
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
				
			}
			if ($(window).width() < 1580 && $(window).width() > 1330) // interval = 3
		    {
		    	interval_X = 3;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    	
		    }
			if ($(window).width() < 1330 && $(window).width() > 1160) // interval = 4
		    {
		    	interval_X = 4;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 1160 && $(window).width() > 1030) // interval = 5
		    {
		    	interval_X = 5;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 1030 && $(window).width() > 980) // interval = 6
		    {
		    	interval_X = 6;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 980 && $(window).width() > 840) // interval = 7
		    {
		    	interval_X = 7;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 840 && $(window).width() > 768) // interval = 8
		    {
		    	interval_X = 8;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 768 && $(window).width()  > 750) // interval = 5
		    {
		    	interval_X = 5;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 750 && $(window).width() > 680)  // interval = 6
		    {
		    	interval_X = 6;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 680 && $(window).width() > 620) // interval = 7
		    {
		    	interval_X = 7;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 620 && $(window).width() > 570) // interval = 8
		    {
		    	interval_X = 8;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 570 && $(window).width() > 510) // interval = 9
		    {
		    	interval_X = 9;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 510 && $(window).width() > 460)  // interval = 10
		    {
		    	interval_X = 10;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 460 && $(window).width() > 440)  // interval = 11
		    {
		    	interval_X = 11;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 440 && $(window).width() > 425) // interval = 12
		    {
		    	interval_X = 12;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		    if ($(window).width() < 425)  // interval = 15
		    {
		    	interval_X = 15;
		    	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
					theme: "light2",
					culture: "es",
					title:{
						text: "Consumo GN (kWh)",
						fontSize: 18,				
						margin: 50,
						fontColor: "#004165"
					},
					exportEnabled: true,
					axisY: {
						suffix: " kWh",
						// minimum: 0,
						// valueFormatString:  "###0.##",
						titleFontColor: "#004165",
						lineColor: "#004165",
						labelFontColor: "#004165",
						tickColor: "#004165"
					},
					axisX: {
						title: "CONSUMO GN "+total1+" kWh",
						interval: interval_X,
						titleFontColor: "#004165",
						titleFontSize: 12,
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
						type: "column",
						visible: true,
						showInLegend: true,
						toolTipContent: "{name}: {y} kWh",
						// yValueFormatString: "###0.## kg CO2 eq",
						name: "Consumo kWh",
						legendMarkerColor: "#004165",
						lineColor: "#004165",
						color: "#004165",
						dataPoints: datapoins2		
					}]
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
		    }
		});
	var chart2 = new CanvasJS.Chart("Consumo_kWh_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Consumo GN (kWh)",
			fontSize: 18,				
			margin: 50,
			fontColor: "#004165"
		},
		exportEnabled: true,
		axisY: {
			suffix: " kWh",
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			title: "CONSUMO GN "+total1+" kWh",
			interval: interval_X,
			titleFontColor: "#004165",
			titleFontSize: 12,
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
			type: "column",
			visible: true,
			showInLegend: true,
			toolTipContent: "{name}: {y} kWh",
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Consumo kWh",
			legendMarkerColor: "#004165",
			lineColor: "#004165",
			color: "#004165",
			dataPoints: datapoins2		
		}]
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

	var canvas = $("#Consumo_Nm3_"+contador+" .canvasjs-chart-canvas").get(0);
	var canvas2 = $("#Consumo_kWh_"+contador+" .canvasjs-chart-canvas").get(0);
	var dataURL = canvas.toDataURL();
	var dataURL2 = canvas2.toDataURL();
	var email = <?php echo json_encode($user->email) ?>;
	var date_from = <?php echo json_encode($date_from) ?>;
	var date_to = <?php echo json_encode($date_to) ?>;
	var conta = <?php echo json_encode($contador_label) ?>;
	//console.log(dataURL);

	$("#exportButton").click(function(){
	    var pdf = new jsPDF("l", "mm", "a4");
	    pdf.setTextColor(51, 51, 51);
	    pdf.text(20, 20, empresa);
	    pdf.setFontSize(11);
	    // pdf.text(20, 30, "Contador: "+contador);
		pdf.text(20, 30, ubicacion);
		pdf.text(20, 37, 'Contador: '+conta);
		pdf.text(20, 44, 'Email: '+email);
		pdf.text(20, 51, 'Intervalo: Desde '+date_from+' hasta '+date_to);
	    var width = pdf.internal.pageSize.width;    
		var height = pdf.internal.pageSize.height;		
	    pdf.addImage(dataURL, 'JPEG', 20, 60, 250, 0);
	    pdf.addImage(dataURL2, 'JPEG', 20, 130, 250, 0);
	    // pdf.setTextColor(0, 65, 101);
	    // pdf.setFontSize(14);
	    // pdf.text(20, height/4+20+30+60, "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq");
	    pdf.save("Consumo_GN.pdf");
	});

</script>