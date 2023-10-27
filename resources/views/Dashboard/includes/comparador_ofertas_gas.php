<script>
		// *******************
		// COMPARADOR OFERTAS
		// *******************

		var ActualVariable = <?php echo json_encode($coste_termino_variable->coste); ?>;
		var ActualFijo = <?php echo json_encode($coste_termino_fijo->coste); ?>; 
		var PropuestoVariable = <?php echo json_encode($coste_termino_variable_propuesto->coste); ?>;
		var PropuestoFijo = <?php echo json_encode($coste_termino_fijo_propuesto->coste); ?>; 

		var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
		var date_from = <?php echo json_encode($date_from) ?>;
		var date_to = <?php echo json_encode($date_to) ?>;
		var conta = <?php echo json_encode($contador_label) ?>;
		var date_from = <?php echo json_encode($date_from) ?>;
		var titulo = <?php echo json_encode($titulo); ?>;	
		
		CanvasJS.addCultureInfo("es", 
	    {      
	      decimalSeparator: ",",// Observe ToolTip Number Format
	      digitGroupSeparator: "."
	    });

		var chart1 = new CanvasJS.Chart("Comparativa_"+contador, {
			// animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			culture: "es",
			title:{
				text: "Comparativa",
				margin: 40,
				fontSize: 18,
				fontColor: "#004165"
			},	
			exportFileName: "Comparativa-"+contador+"-"+date_from+"-"+date_to,
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
				title: '',
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
				legendText: "Situación Actual", // Label del legend
				toolTipContent: "{name}: {y} €",
				legendMarkerColor: "#004165",
				showInLegend: true,
				bevelEnabled: true,

				dataPoints:[
					{ label: 'Coste Término Variable (€)', y: parseInt(ActualVariable), color:"#004165" },
					{ label: 'Coste Término Fijo (€)', y: parseInt(ActualFijo), color:"#004165" },
					{ label: 'Coste Total (€)', y: parseInt(ActualVariable)+parseInt(ActualFijo), color:"#004165" },					
				]
			},
			{
				type: "column",	
				name: "Coste Propuesta", // Label del cursor
				legendText: "Situación Propuesto", // Label del legend
				legendMarkerColor: "#B9C9D0",
				toolTipContent: "{name}: {y} €",
				showInLegend: true,
				bevelEnabled: true,
				dataPoints:[
					
					{ label: 'Coste Término Variable (€)', y: parseInt(PropuestoVariable), color:"#B9C9D0" },
					{ label: 'Coste Término Fijo (€)', y: parseInt(PropuestoFijo), color:"#B9C9D0" },
					{ label: 'Coste Total (€)', y: parseInt(PropuestoVariable)+parseInt(PropuestoFijo), color:"#B9C9D0" },
				]
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

		var canvas1 = $("#Comparativa_"+contador+" .canvasjs-chart-canvas").get(0);
		var dataURL1 = canvas1.toDataURL();
		
		var empresa = "Empresa: "+<?php echo json_encode($user->name); ?>;
		var ubicacion = "Ubicación: "+<?php echo json_encode($domicilio->suministro_del_domicilio);?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
		var email = <?php echo json_encode($user->email) ?>;
		var date_from = <?php echo json_encode($date_from) ?>;
		var date_to = <?php echo json_encode($date_to) ?>;
		var conta = <?php echo json_encode($contador_label) ?>;
		//console.log(dataURL);
		
		
		$("#exportButton").click(function(){
		    var pdf = new jsPDF("p", "mm", "a4");
		    var width = pdf.internal.pageSize.width;
		    console.log(width);
		    pdf.setTextColor(51, 51, 51);
		    pdf.text(20, 20, empresa);		    
		    pdf.setFontSize(11);
			pdf.text(20, 30, ubicacion);
			pdf.text(20, 37, 'Contador: '+conta);
			pdf.text(20, 44, 'Email: '+email);
		    pdf.text(20, 51, 'Intervalo: Desde '+date_from+' hasta '+date_to);
		    pdf.addImage(dataURL1, 'JPEG', 20, 60, 0, width-170);
		    var titulo = "Comparador de Ofertas";		    
		    
		    pdf.save(titulo+"_"+contador+"_"+date_from+"_"+date_to+".pdf");
		});
			
	</script>