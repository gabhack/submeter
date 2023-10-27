	<script>
		// *******************
		// COMPARADOR OFERTAS
		// *******************

		var arrayActualE = <?php echo json_encode($coste_actual_energia); ?>;
		console.log('arrayActualE',arrayActualE);
		var arrayPropuestoE = <?php echo json_encode($coste_propuesto_energia); ?>;
		var tipo_tarifa = <?php echo json_encode($tipo_tarifa); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $contador_label))) ?>;
		var titulo = <?php echo json_encode($titulo); ?>;
		var date_from = <?php echo json_encode($date_from); ?>;
		var date_to = <?php echo json_encode($date_to); ?>;
		var sumaActualE = <?php echo json_encode($total_actual_energia) ?>;		
		var sumaPropuestoE = <?php echo json_encode($total_propuesto_energia) ?>;
		var sumaActualE2 = <?php echo json_encode(number_format($total_actual_energia,2,',','.')) ?>;		
		var sumaPropuestoE2 = <?php echo json_encode(number_format($total_propuesto_energia,2,',','.')) ?>;
		var datapoins1 = [];
		var datapoins2 = [];

		for (var i = 0; i < arrayActualE.length; i++)
		{
			if(tipo_tarifa == 1)
			{
				datapoins1.push({ label: 'P'+(i+1), y: parseInt(arrayActualE[i]['coste_energia']), color:"#004165" });
				datapoins2.push({ label: 'P'+(i+1), y: parseInt(arrayActualE[i]['coste_energia_propuesto']), color:"#B9C9D0" });
			}
			else{
				if(tipo_tarifa != 1)
				{
					datapoins1.push({ label: 'P'+(i+1), y: parseInt(arrayActualE[i]), color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: parseInt(arrayPropuestoE[i]), color:"#B9C9D0" });	
				}
			}
		}
		if(arrayActualE.length == 0)
		{
			if(tipo_tarifa == 1)
			{
				for (var i = 0; i < 6; i++) 
				{
					datapoins1.push({ label: 'P'+(i+1), y: 0, color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: 0, color:"#B9C9D0" });	
				}
			}else{
				for (var i = 0; i < 3; i++) 
				{
					datapoins1.push({ label: 'P'+(i+1), y: 0, color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: 0, color:"#B9C9D0" });
				}
			}
		}
		datapoins1.push({ label: 'TOTAL', y: parseInt(sumaActualE),   color:"#004165" });
		datapoins2.push({ label: 'TOTAL', y: parseInt(sumaPropuestoE), color:"#B9C9D0" });
		
		CanvasJS.addCultureInfo("es", 
	    {      
	      decimalSeparator: ",",// Observe ToolTip Number Format
	      digitGroupSeparator: "."
	    });

		var chart1 = new CanvasJS.Chart("TerminoEnergia_"+contador, {
			// animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			culture: "es",
			title:{
				text: "Coste Termino Energía",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			exportFileName: "CosteTerminoEnergía-"+contador+"-"+date_from+"-"+date_to,
			exportEnabled: true,
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				// valueFormatString: "#####0.##",
				suffix: " €",
				tickColor: "#004165"
			},
			axisX: {
				title: 'Total Propuesta: '+sumaPropuestoE2+' € Total Actual: '+sumaActualE2+' €',
				titleFontSize: 12,
				titleMaxWidth: 180,
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
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Actual",//Label del cursor
				legendText: "Coste Actual", // Label del legend
				toolTipContent: "{name}: {y} €",
				legendMarkerColor: "#004165",
				showInLegend: true,
				bevelEnabled: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Propuesto", // Label del cursor
				legendText: "Coste Propuesto", // Label del legend
				legendMarkerColor: "#B9C9D0",
				toolTipContent: "{name}: {y} €",
				showInLegend: true,
				bevelEnabled: true,
				dataPoints:datapoins2
			},
			]
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
		// *******************
		// COMPARADOR OFERTAS
		// *******************

		var arrayActualP = <?php echo json_encode($coste_actual_potencia); ?>;
		var arrayPropuestoP = <?php echo json_encode($coste_propuesto_potencia); ?>;
		console.log(arrayActualP);
		console.log(arrayPropuestoP);

		var contador = <?php echo json_encode(implode('_', explode(' ', $contador_label))) ?>;
		var sumaActualP = <?php echo json_encode($total_actual_potencia) ?>;
		var sumaPropuestoP = <?php echo json_encode($total_propuesto_potencia) ?>;
		var sumaActualP2 = <?php echo json_encode(number_format($total_actual_potencia,2,',','.')) ?>;
		var sumaPropuestoP2 = <?php echo json_encode(number_format($total_propuesto_potencia,2,',','.')) ?>;
		var datapoins1 = [];
		var datapoins2 = [];
		for (var i = 0; i < arrayActualP.length; i++)
		{
			if(tipo_tarifa == 1)
			{
				datapoins1.push({ label: 'P'+(i+1), y: parseInt(arrayActualP[i]['coste_potencia']), color:"#004165"});
				datapoins2.push({ label: 'P'+(i+1), y: parseInt(arrayActualP[i]['coste_potencia_propuesto']), color:"#B9C9D0" });
			}
			else{
				if(tipo_tarifa != 1)
				{
					datapoins1.push({ label: 'P'+(i+1), y: parseInt(arrayActualP[i]), color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: parseInt(arrayPropuestoP[i]), color:"#B9C9D0" });	
				}
			}
		}
		if(arrayActualP.length == 0)
		{
			if(tipo_tarifa == 1)
			{
				for (var i = 0; i < 6; i++) 
				{
					datapoins1.push({ label: 'P'+(i+1), y: 0, color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: 0, color:"#B9C9D0" });	
				}
			}else{
				for (var i = 0; i < 3; i++) 
				{
					datapoins1.push({ label: 'P'+(i+1), y: 0, color:"#004165" });
					datapoins2.push({ label: 'P'+(i+1), y: 0, color:"#B9C9D0" });
				}
			}
		}
		datapoins1.push({ label: 'TOTAL', y: parseInt(sumaActualP),   color:"#004165" });
		datapoins2.push({ label: 'TOTAL', y: parseInt(sumaPropuestoP), color:"#B9C9D0" });

		CanvasJS.addCultureInfo("es", 
	    {      
	      decimalSeparator: ",",
	      digitGroupSeparator: "."
	    });

		var chart2 = new CanvasJS.Chart("TerminoPotencia_"+contador, {
			// animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			culture: "es",
			title:{
				text: "Coste Termino Potencia",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			exportFileName: "CosteTerminoPotencia-"+contador+"-"+date_from+"-"+date_to,
			exportEnabled: true,
			axisY: {				
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",

				// valueFormatString:  "#####0.##",
				suffix: " €",
				tickColor: "#004165"
			},
			axisX: {
				titleMaxWidth: 180,
				title: 'Total Propuesta: '+sumaPropuestoP2+' € Total Actual: '+sumaActualP2+' €',
				titleFontSize: 12,
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
				itemclick: toggleDataSeries,				
			},
			data: [{
				type: "column",
				name: "Coste Actual",//Label del cursor
				legendText: "Coste Actual", // Label del legend
				legendMarkerColor: "#004165",
				toolTipContent: "{name}: {y} €",
				// yValueFormatString: "#####0.## €",
				showInLegend: true,
				bevelEnabled: true,

				dataPoints:datapoins1
			},
			{
				type: "column",	
				name: "Coste Propuesto", // Label del cursor
				legendText: "Coste Propuesto", // Label del legend
				legendMarkerColor: "#B9C9D0",
				toolTipContent: "{name}: {y} €",
				showInLegend: true,
				bevelEnabled: true,
				dataPoints:datapoins2
			},
			]
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
		var canvas1 = $("#TerminoEnergia_"+contador+" .canvasjs-chart-canvas").get(0);
		var dataURL1 = canvas1.toDataURL();
		var canvas2 = $("#TerminoPotencia_"+contador+" .canvasjs-chart-canvas").get(0);
		var dataURL2 = canvas2.toDataURL();
		var empresa = "Empresa: "+<?php echo json_encode($user->name); ?>;
		var ubicacion = "Ubicación: "+<?php echo json_encode(($domicilio->suministro_del_domicilio)?$domicilio->suministro_del_domicilio:''); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $contador_label))) ?>;
		var email = <?php echo json_encode($user->email) ?>;
		var date_from = <?php echo json_encode($date_from) ?>;
		var date_to = <?php echo json_encode($date_to) ?>;
		var conta = <?php echo json_encode($contador_label) ?>;
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
    		
    		var cntChart = $(".plot-tab");
    		var handleCharts = [];
    		var dataCharts = [];
    
    		var idxElement = 1;
    		
    		for(var i = 0; i < cntChart.length; i++){
    			var canvas = $(cntChart[i]).find("canvas")[0];
    			var data = canvas.toDataURL('image/jpeg', 1.0);
    			dataCharts.push(data);
    		}
    
    		
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
		/*$("#exportButton").click(function(){
		    var pdf = new jsPDF("p", "mm", "a4");
		    var width = pdf.internal.pageSize.width;
		    pdf.setTextColor(51, 51, 51);
		    pdf.text(20, 20, empresa);		    
		    pdf.setFontSize(11);
			pdf.text(20, 30, ubicacion);
			pdf.text(20, 37, 'Contador: '+conta);
			pdf.text(20, 44, 'Email: '+email);
		    pdf.text(20, 51, 'Intervalo: Desde '+date_from+' hasta '+date_to);
		    pdf.addImage(dataURL1, 'JPEG', 20, 60, 0, width-126);		    
		    pdf.addImage(dataURL2, 'JPEG', 20, 160, 0, width-126);		    
		    pdf.save("Comparador_Ofertas.pdf");
		});*/
	</script>