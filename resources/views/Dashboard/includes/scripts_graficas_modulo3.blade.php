
	<script>	

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
		
		var arrayEje = <?php echo json_encode($eje); ?>;
		var intervalo = <?php echo json_encode($label_intervalo); ?>;
		var dates = <?php echo json_encode($dates); ?>;
		var arrayConsumoActiva = <?php echo json_encode($consumo_activa); ?>;		
		var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
		var totalEA = <?php echo json_encode(number_format($totalActiva,0,',','.')); ?>;
		var datapoins1 = [];
		var aux_Conteo = 0;
		var date_to = <?php echo json_encode($date_to); ?>;
		var date_from = <?php echo json_encode($date_from); ?>;
		var number_days = <?php echo json_encode($number_days); ?>;
		var aux_label = '';
		var aux_interval;
		var pos_outside;
		var index_label;
		var index_label_orientation;
		var aux_max_consumo = <?php echo json_encode($aux_max_consumo); ?>;


		if(intervalo == 'Personalizado' && date_to !== date_from && number_days < 10)
		{			
			arrayEje = dates;
			pos_outside = "outside";
			index_label = "{y}"+" kWh";
			index_label_orientation = "horizontal";
		}else{
			if(intervalo == 'Personalizado' && date_to !== date_from && number_days >= 10)
			{
				arrayEje = dates;
				pos_outside = "outside";
				index_label = "";
				index_label_orientation = "horizontal";
			}
		}

		for (var i = 0; i < arrayEje.length; i++) {										
			datapoins1.push({ label: ((arrayEje[i])), y: parseInt(arrayConsumoActiva[i])*1, color:"#004165", click: clickDataSeries });					
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
						datapoins1.push({ label: "0"+k+":00", y: 0, color:"#004165" });					
					}else{
						if(k >= 10)
						{
							datapoins1.push({ label: (k)+":00", y: 0, color:"#004165" });						
						}else{							
							datapoins1.push({ label: ((arrayEje[i])), y: parseInt(arrayConsumoActiva[i]), color:"#004165", click: clickDataSeries });
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
						// console.log('el punto del eje');
						// console.log(arrayEje[k-2]);
						if(arrayEje[aux_Conteo-1] < k)
						{
							datapoins1.push({ label: k, y: 0, color:"#004165", click: clickDataSeries });
					
						}else{
							if(aux_Conteo == 0)
							{
								datapoins1.push({ label: k, y: 0, color:"#004165", click: clickDataSeries });					
							}
						}						
					}
				}
			}else{
				if(intervalo == "Semana Actual" || intervalo == "Semana Anterior")
				{
					aux_label = "Día: ";
					var aux_dias = [];
					aux_dias[0] = 'Lunes';
					aux_dias[1] = 'Martes';
					aux_dias[2] = 'Miércoles';
					aux_dias[3] = 'Jueves';
					aux_dias[4] = 'Viernes';
					aux_dias[5] = 'Sábado';
					aux_dias[6] = 'Domingo';
					pos_outside = "outside";
					index_label = "{y}"+" kWh";
					index_label_orientation = "horizontal";

					if(aux_Conteo < 7)
					{
						for(var k = aux_Conteo; k < 7; k++)
						{
							datapoins1.push({ label: aux_dias[k], y: 0, color:"#004165", click: clickDataSeries },);
						}
					}
				}else{
					if(intervalo == "Ultimo Trimestre" || intervalo == "Trimestre Actual")
					{
						aux_label = "Mes: ";
						pos_outside = "outside";
						index_label = "{y}"+" kWh";
						index_label_orientation = "horizontal";
					}else{
						if(intervalo == "Último Año" || intervalo == "Año Actual")
						{
							aux_label = "Mes: ";
							pos_outside = "outside";
							index_label = " ";
							index_label_orientation = "horizontal";
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
		}
		
		var aux1 = parseFloat(parseInt(aux_max_consumo).toExponential().split("e")[0]).toString();
		var aux2 = aux1.split(".");
		var aux3 = "0."+aux2[1];
		var maximo = 1;
		var div;

		console.log('Exponencial: '+Math.pow(10, (parseInt(aux_max_consumo).toExponential().split("e")[1])));

		if(parseFloat(aux3) < 0.5)
		{
			maximo = parseInt((parseInt(aux2[0])+0.5)*Math.pow(10,parseInt(parseInt(aux_max_consumo).toExponential().split("e")[1])))+(Math.pow(10, (parseInt(aux_max_consumo).toExponential().split("e")[1]))/2);
			// console.log(maximo);				
		}
		else{
			maximo = parseInt((parseInt(aux2[0])+1)*Math.pow(10,parseInt(parseInt(aux_max_consumo).toExponential().split("e")[1])))+(Math.pow(10, (parseInt(aux_max_consumo).toExponential().split("e")[1])))/2;
			// console.log(parseInt(aux2[0])+1);
		}
		console.log(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10);
		if(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10%3 == 0)
		{
			div = maximo/3;
		}else{
			if(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10%4 == 0)
			{
				div = maximo/4;
			}else{
				// if(parseFloat((maximo.toExponential(1)).toString().split("e")[0])*10%3 == 0)
				// {
					div = maximo/5;
				// }
			}
			
		}

		console.log('Maximo: '+maximo, 'Div: '+div);

		CanvasJS.addCultureInfo("es", 
	    {      
	    	decimalSeparator: ",",// Observe ToolTip Number Format
	    	digitGroupSeparator: "." // Observe axisY labels
	  	});


		var chart1 = new CanvasJS.Chart("ConsumoActiva_"+contador, {
			animationEnabled: false,
			theme: "light2",
			culture: "es",
			title:{
				text: "Consumo Energía Activa ",
				fontSize: 18,				
				margin: 30,
				fontColor: "#004165"
			},
			exportEnabled: true,
			axisY: {
				suffix: " kWh",
				// valueFormatString:  "###0.##",
				maximum: maximo+parseInt(parseInt(aux_max_consumo).toExponential().split("e")[1]),
				interval: div,
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165"
			},	
			axisX: {
				title: 'Total de Energía Activa: '+totalEA+' kWh',
				titleFontSize: 12,
				titleFontColor: "#004165",
				lineColor: "#004165",
				interval: aux_interval,
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
				cursor: "zoom-in",
				type: "column",
				name: "Energía Activa",//Label del cursor
				legendText: "Energía Activa", // Label del legend
				legendMarkerColor: "#004165",
				toolTipContent: aux_label+"{label} </br> {name}: {y} kWh",
				indexLabelFontColor: "#004165",
				indexLabelPlacement: pos_outside,
		        indexLabel: index_label,
		        indexLabelOrientation: index_label_orientation,
		        // indexLabelMaxWidth: 60,
				// yValueFormatString: "###0.## kWh",
				showInLegend: true, 
				bevelEnabled: true,
				dataPoints: datapoins1
			}]
		});
		chart1.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart1.render();
		}

	</script>

	<script>	
		
		var arrayEje = <?php echo json_encode($eje); ?>;
		var arrayConsumoActiva = <?php echo json_encode($consumo_activa); ?>;		
		var tipo_count = <?php echo json_encode($tipo_count); ?>;
		var arrayGeneracion = <?php echo json_encode($db_Generacion); ?>;
		var totalGeneracion = <?php echo json_encode(number_format($total_generacion,0,',','.')); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
		var totalEA = <?php echo json_encode(number_format($totalActiva,0,',','.')); ?>;
		var datapoins1 = [];
		var aux_Conteo = 0;

		if(tipo_count == 2)
		{
			if(intervalo == 'Personalizado' && date_to !== date_from)
			{
				arrayEje = dates;
				pos_outside = "outside";
				index_label = "{y}"+" kWh";
				index_label_orientation = "horizontal";
			}

			for (var i = 0; i < arrayEje.length; i++) {										
				datapoins1.push({ label: ((arrayEje[i])), y: parseInt(arrayGeneracion[i]["generacion_energia"])*1, color:"#B9C9D0", click: clickDataSeries });
				aux_Conteo++;				
			}

			if(intervalo == "Ayer" || intervalo == "Hoy" || (intervalo == 'Personalizado' && date_to == date_from))
			{
				aux_label = "Hora: ";
				if(aux_Conteo < 24)
				{
					for(var k = aux_Conteo; k < 24; k++)
					{
						if(k < 10)
						{
							datapoins1.push({ label: "0"+k+":00", y: 0, color:"#B9C9D0", click: clickDataSeries });
						}else{
							if(k >= 10)
							{
								datapoins1.push({ label: k+":00", y: 0, color:"#B9C9D0", click: clickDataSeries });							
							}
						}
					}
				}
			}

			if(intervalo == "Semana Actual" || intervalo == "Semana Anterior")
			{
				aux_label = "Día: ";
				var aux_dias = [];
				aux_dias[0] = 'Lunes';
				aux_dias[1] = 'Martes';
				aux_dias[2] = 'Miércoles';
				aux_dias[3] = 'Jueves';
				aux_dias[4] = 'Viernes';
				aux_dias[5] = 'Sábado';
				aux_dias[6] = 'Domingo';
				pos_outside = "outside";
				index_label = "{y}"+" kWh";
				index_label_orientation = "horizontal";

				if(aux_Conteo < 7)
				{
					for(var k = aux_Conteo; k < 7; k++)
					{
						datapoins1.push({ label: aux_dias[k], y: 0, color:"#B9C9D0", click: clickDataSeries },);
					}
				}
			}

			if(intervalo == "Mes Actual" || intervalo == "Mes Anterior")
			{
				aux_label = "Día: ";
				aux_interval = 1;
				var aux_day = date_to.split('-');
				
				if(aux_Conteo < aux_day[2])
				{
					for(var k = aux_Conteo+1; k <= parseInt(aux_day[2]); k++)
					{
						// console.log('el punto del eje');
						// console.log(arrayEje[k-2]);
						if(arrayEje[aux_Conteo-1] < k)
						{
							datapoins1.push({ label: k, y: 0, color:"#004165", click: clickDataSeries });
						}else{
							if(aux_Conteo == 0)
							{
								datapoins1.push({ label: k, y: 0, color:"#004165", click: clickDataSeries });							
							}
						}						
					}
				}
			}
			if(intervalo == "Ultimo Trimestre" || intervalo == "Trimestre Actual")
			{
				aux_label = "Mes: ";
				pos_outside = "outside";
				index_label = "{y}"+" kWh";
				index_label_orientation = "horizontal";
				if(aux_Conteo < 3)
				{
					for(var k = aux_Conteo; k <= 3; k++)
					{
						datapoins1.push({ label: arrayEje[k], y: 0, color:"#B9C9D0", click: clickDataSeries });
					}
				}
			}
			if(intervalo == "Último Año" || intervalo == "Año Actual")
			{
				aux_label = "Mes: ";
				pos_outside = "outside";
				index_label = "";
				index_label_orientation = "horizontal";
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

			CanvasJS.addCultureInfo("es", 
		    {      
		    	decimalSeparator: ",",// Observe ToolTip Number Format
		    	digitGroupSeparator: "." // Observe axisY labels
		  	});


			var chart1 = new CanvasJS.Chart("Generacion_"+contador, {
				animationEnabled: false,
				theme: "light2",
				culture: "es",
				title:{
					text: "Generación Energía",
					fontSize: 18,				
					margin: 50,
					fontColor: "#004165"
				},
				exportEnabled: true,
				axisY: {
					suffix: " kWh",
					// valueFormatString:  "###0.##",
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
				tickColor: "#004165"
				},	
				axisX: {
				title: 'Total Generación Energía: '+totalGeneracion+' kWh',
				titleFontSize: 12,
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				interval: aux_interval,
				tickColor: "#B9C9D0"
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
					name: "Generación Energía",//Label del cursor
					legendText: "Generación Energía", // Label del legend
					legendMarkerColor: "#B9C9D0",
					toolTipContent: aux_label+"{label} </br> {name}: {y} kWh",
					indexLabelFontColor: "#004165",
					indexLabelPlacement: pos_outside,
			        indexLabel: index_label,
			        indexLabelOrientation: index_label_orientation,
					// yValueFormatString: "###0.## kWh",
					showInLegend: true, 
					bevelEnabled: true,
					dataPoints: datapoins1
				}]
			});
			chart1.render();

			function toggleDataSeries(e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				}
				else {
					e.dataSeries.visible = true;
				}
				chart1.render();
			}
		}

		

	</script>

	<script>	
		
		var arrayEje = <?php echo json_encode($eje); ?>;
		
		var arrayConsumoCapacitiva = <?php echo json_encode($consumo_cap); ?>;
		var arrayConsumoInductiva = <?php echo json_encode($consumo_induc); ?>;
		var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
		var totalCap = <?php echo json_encode(number_format($totalCapa,0,',','.')) ?>;
		var totalInduc = <?php echo json_encode(number_format($totalInduc,0,',','.')) ?>;
		var datapoins2 = [];
		var datapoins3 = [];
		var aux_Conteo = 0;
		if(intervalo == 'Personalizado' && date_to !== date_from && number_days < 10)
		{
			arrayEje = dates;
			pos_outside = "outside";
			index_label = "{y}"+" kWArh";
			index_label_orientation = "horizontal";
		}else{
			if(intervalo == 'Personalizado' && date_to !== date_from && number_days >= 10)
			{
				arrayEje = dates;
				pos_outside = "outside";
				index_label = "";
				index_label_orientation = "horizontal";
			}
		}
		CanvasJS.addCultureInfo("es", 
	    {      
	    	decimalSeparator: ",",// Observe ToolTip Number Format
	    	digitGroupSeparator: "." // Observe axisY labels
	  	});
		for (var i = 0; i < arrayEje.length; i++) {
										
			datapoins2.push({ label: arrayEje[i], y: parseInt(arrayConsumoInductiva[i]), color:"#B9C9D0", click: clickDataSeries },);
			datapoins3.push({ label: arrayEje[i], y: parseInt(arrayConsumoCapacitiva[i]), color:"#7D9AAA", click: clickDataSeries });
			aux_Conteo++;
						
		}

		if(intervalo == "Ayer" || intervalo == "Hoy" || (intervalo == 'Personalizado' && date_to == date_from))
		{
			aux_label = "Hora: ";
			
			if(aux_Conteo < 24)
			{
				for(var k = aux_Conteo; k < 24; k++)
				{
					if(k < 10)
					{
						datapoins2.push({ label: "0"+k+":00", y: 0, color:"#B9C9D0", click: clickDataSeries },);
						datapoins3.push({ label: "0"+k+":00", y: 0, color:"#7D9AAA", click: clickDataSeries });						
					}else{
						if(k >= 10)
						{
							datapoins2.push({ label: k+":00", y: 0, color:"#B9C9D0", click: clickDataSeries },);
							datapoins3.push({ label: k+":00", y: 0, color:"#7D9AAA", click: clickDataSeries });
						}
					}
				}
			}
		}

		if(intervalo == "Mes Actual" || intervalo == "Mes Anterior")
		{
			aux_label = "Día: ";
			aux_interval = 1;
			
			var aux_day = date_to.split('-');
			if(aux_Conteo < aux_day[2])
			{
				for(var k = aux_Conteo+1; k <= parseInt(aux_day[2]); k++)
				{
					// console.log('el punto del eje');
					// console.log(arrayEje[k-2]);
					if(arrayEje[aux_Conteo-1] < k)
					{						
						datapoins2.push({ label: k, y: 0, color:"#B9C9D0", click: clickDataSeries },);
						datapoins3.push({ label: k, y: 0, color:"#7D9AAA", click: clickDataSeries });
					}else{
						if(aux_Conteo == 0)
						{							
							datapoins2.push({ label: k, y: 0, color:"#B9C9D0", click: clickDataSeries },);
							datapoins3.push({ label: k, y: 0, color:"#7D9AAA", click: clickDataSeries });
						}
					}						
				}
			}
		}

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
			pos_outside = "outside";
			index_label = "{y}"+" kWArh";
			index_label_orientation = "horizontal";

			if(aux_Conteo < 7)
			{
				for(var k = aux_Conteo; k < 7; k++)
				{
					datapoins2.push({ label: aux_dias[k], y: 0, color:"#B9C9D0", click: clickDataSeries },);
					datapoins3.push({ label: aux_dias[k], y: 0, color:"#7D9AAA", click: clickDataSeries });
				}

			}
		}

		if(intervalo == "Ultimo Trimestre" || intervalo == "Último Año")
		{
			aux_label = "Mes: ";
			pos_outside = "outside";
			index_label = " ";
			index_label_orientation = "horizontal";
		}

		var chart2 = new CanvasJS.Chart("ConsumoReactiva_"+contador, {
			animationEnabled: false,
			theme: "light2",
			culture: "es",
			title:{
				text: "Consumo Energía Reactiva",
				fontSize: 18,				
				margin: 50,
				fontColor: "#004165"
			},
			exportEnabled: true,
			axisY: {
				suffix: " kVArh",
				// valueFormatString:  "###0.##",
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				tickColor: "#004165"
			},	
			axisX: {
				title: 'Total Consumo Capacitiva: '+totalCap+' kVArh. \n\n\n\n\n\n\n\n\n\n\n\nTotal Consumo Inductiva: '+totalInduc+ ' kVArh',
				titleFontSize: 12,
				labelAutoFit: true,
				titleFontColor: "#004165",
				lineColor: "#004165",
				labelFontColor: "#004165",
				interval: aux_interval,
				tickColor: "#004165"
			},
			toolTip: {
				shared: true
			},
			legend: {
				cursor:"pointer",
				itemclick: toggleDataSeries
			},
			data: [
			{
				cursor: "zoom-in",
				type: "column",	
				name: "Energía Reactiva Inductiva", // Label del cursor
				legendText: "Energía Reactiva Inductiva", // Label del legend
				legendMarkerColor: "#B9C9D0",				
				toolTipContent: aux_label+"{label} </br> {name}: {y} kWArh",
				indexLabelFontColor: "#004165",
				indexLabelPlacement: pos_outside,
		        indexLabel: index_label,
		        indexLabelOrientation: index_label_orientation,
		        // indexLabelMaxWidth: 50,
				// axisYType: "secondary",
				showInLegend: true,
				bevelEnabled: true,
				dataPoints:datapoins2
			},
			{
				type: "column",	
				name: "Energía Reactiva Capacitiva", // Label del cursor
				legendText: "Energía Reactiva Capacitiva", // Label del legend
				toolTipContent: "{name}: {y} kVArh",
				legendMarkerColor: "#7D9AAA",
				// axisYType: "secondary",
				indexLabelPlacement: pos_outside,
		        indexLabel: index_label,
		        indexLabelOrientation: index_label_orientation,
		        indexLabelFontColor: "#004165",
		        // indexLabelMaxWidth: 50,
				showInLegend: true,
				bevelEnabled: true,
				dataPoints:datapoins3
			}]
		});
		chart2.render();

		function toggleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				e.dataSeries.visible = false;
			}
			else {
				e.dataSeries.visible = true;
			}
			chart2.render();
		}

	</script>

	<script>
		function ejecutar_consulta(label_contador){
			// ***********************************
			// CONSUMO ACTIVA
			// ***********************************			
			var arrayEje = <?php echo json_encode($eje); ?>;
			var arrayConsumoActiva = <?php echo json_encode($consumo_activa); ?>;		
			var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
			var datapoins1 = [];
			for (var i = 0; i < arrayEje.length; i++) {
				datapoins1.push({ label: arrayEje[i], y: parseInt(arrayConsumoActiva[i]), color:"#004165" });			
			}			
			var chart1 = new CanvasJS.Chart("ConsumoActiva_"+contador, {
				animationEnabled: false,
				theme: "light2",
				title:{
					text: "Consumo Energía Activa",
					fontSize: 18,				
					margin: 50,
					fontColor: "#004165"
				},
				exportEnabled: true,
				axisX: {
					titleFontColor: "#004165",
					lineColor: "#004165",
					labelFontColor: "#004165",
					tickColor: "#004165"
				},
				axisY: {
					suffix: " kWh",
					valueFormatString:  "###0.##",
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
					name: "Energía Activa",//Label del cursor
					legendText: "Energía Activa", // Label del legend
					legendMarkerColor: "#004165",
					showInLegend: true, 
					yValueFormatString: "###0.## kWh",
					dataPoints: datapoins1
				}]
			});
			chart1.render();

			function toggleDataSeries(e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				}
				else {
					e.dataSeries.visible = true;
				}
				chart1.render();
			}

			// ***********************************
			// CONSUMO REACTIVA
			// ***********************************
			
			var arrayEje = <?php echo json_encode($eje2); ?>;
		
			var arrayConsumoCapacitiva = <?php echo json_encode($consumo_cap); ?>;
			var arrayConsumoInductiva = <?php echo json_encode($consumo_induc); ?>;
			var contador = <?php echo json_encode(implode('_', explode(' ', $user->_count[0]->count_label))) ?>;
			var totalCap = <?php echo json_encode(number_format($totalCapa,0,',','.')) ?>;
			var totalInduc = <?php echo json_encode(number_format($totalInduc,0,',','.')) ?>;
			
			var datapoins2 = [];
			var datapoins3 = [];
			for (var i = 0; i < arrayEje.length; i++) {
				
				datapoins2.push({ label: arrayEje[i], y: parseInt(arrayConsumoInductiva[i]), color:"#B9C9D0" },);
				datapoins3.push({ label: arrayEje[i], y: parseInt(arrayConsumoCapacitiva[i]), color:"#7D9AAA" });
			}			
			var chart2 = new CanvasJS.Chart("ConsumoReactiva_"+contador, {
				animationEnabled: false,
				theme: "light2",
				title:{
					text: "Consumo Energía Reactiva",
					fontSize: 18,				
					margin: 50,
					fontColor: "#004165"
				},
				exportEnabled: true,
				axisY: {
					suffix: " kVArh",
					valueFormatString:  "###0.##",
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
				data: [
				{
					type: "column",	
					name: "Energía Reactiva Inductiva", // Label del cursor
					legendText: "Energía Reactiva Inductiva", // Label del legend
					legendMarkerColor: "#B9C9D0",
					// yValueFormatString: "###0.## kVArh",
					// axisYType: "secondary",
					showInLegend: true,
					dataPoints:datapoins2
				},
				{
					type: "column",	
					name: "Energía Reactiva Capacitiva", // Label del cursor
					legendText: "Energía Reactiva Capacitiva", // Label del legend
					legendMarkerColor: "#7D9AAA",
					// yValueFormatString: "###0.## kVArh",
					// axisYType: "secondary",
					showInLegend: true,
					dataPoints:datapoins3
				}]
			});
			chart2.render();

			function toggleDataSeries(e) {
				if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
				}
				else {
					e.dataSeries.visible = true;
				}
				chart2.render();
			}
		}		
	</script>

	<script>
		var canvas2 = $("#ConsumoActiva_"+contador+" .canvasjs-chart-canvas").get(0);		
		var tipo_count = <?php echo json_encode($tipo_count) ?>;
		if(tipo_count == 1){
			var canvas1 = $("#ConsumoReactiva_"+contador+" .canvasjs-chart-canvas").get(0);
		}else{
			var canvas1 = $("#Generacion_"+contador+" .canvasjs-chart-canvas").get(0);
		}

		var dataURL1 = canvas1.toDataURL('image/jpeg', 1.0);
		var dataURL2 = canvas2.toDataURL('image/jpeg', 1.0);
		
		var empresa = <?php echo json_encode($user->name) ?>;
		var email = <?php echo json_encode($user->email) ?>;
		var date_from = <?php echo json_encode($date_from) ?>;
		var date_to = <?php echo json_encode($date_to) ?>;
		var conta = <?php echo json_encode($contador_label) ?>;
		var ubicacion = <?php echo json_encode($ubi) ?>;
		//console.log(dataURL);

		$("#exportButton").click(function(){
			console.log('boton imprimir');
		    var pdf = new jsPDF("l", "mm", [297, 210]);
		    pdf.setTextColor(51, 51, 51);
		    pdf.text(20, 20, 'Empresa: '+empresa);
		    pdf.setFontSize(11);
		    pdf.text(20, 30, 'Ubicación: '+ubicacion);
		    pdf.text(20, 37, 'Contador: '+conta);
		    pdf.text(20, 44, 'Email: '+email);
		    pdf.text(20, 51, 'Intervalo: Desde '+date_from+' hasta '+date_to);
		    var width = pdf.internal.pageSize.width;    
			var height = pdf.internal.pageSize.height;
			pdf.addImage(dataURL1, 'JPG', 20, 130, width-40, 60, '1','FAST');
			pdf.addImage(dataURL2, 'JPG', 20, 60, width-40, 60, '2','FAST');
		    //
		    //pdf.text(20, height/4+20+30+80, "Total Consumo Inductiva: "+ totalInduc +" kVArh");
		    //pdf.text(20, height/4+20+30+90, "Total Consumo Capacitiva: "+ totalCap +" kVArh");
		    pdf.save("Consumo_Energia.pdf");
		});
	</script>