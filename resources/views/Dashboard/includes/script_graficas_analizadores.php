<script>

	var label_intervalo = <?php echo json_encode($label_intervalo); ?>;
	var contador = <?php echo json_encode(implode('_', explode(' ', $contador2->count_label))) ?>;
	var titulo = <?php echo json_encode($titulo); ?>;
	var date_from = <?php echo json_encode($date_from); ?>;
	var date_to = <?php echo json_encode($date_to); ?>;
	var td_aux = <?php echo json_encode($td_aux); ?>;

//-------
	var td_PotAct_total = <?php echo json_encode($td_PotAct_total); ?>;
	var td_PotReac_total = <?php echo json_encode($td_PotReac_total); ?>;
	var datapoins1 = [];
	var datapoins2 = [];
	var datapoins3 = [];
	var aux_legend_tooltip = "Hora: {z}, {name}: {y} kW";
	var aux_interval = 1;
	var aux_intervaltype =null;
	var aux_valueFormatString = null;


			if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){

				for (var i = 0; i < td_PotAct_total.length; i++) {
					datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotAct_total[i]['POWact_total']), z: td_aux[i]['time']});
					datapoins2.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotReac_total[i]['POWrea_total']), z: td_aux[i]['time']});
				}
					aux_valueFormatString = "HH:mm";
					aux_intervaltype = "hour";

			}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
				for (var i = 0; i < td_PotAct_total.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_total[i]['POWact_total']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
					datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac_total[i]['POWrea_total']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				}
					aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} kW";
					aux_interval = 288;
					aux_valueFormatString = "HH:mm";

			}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
				for (var i = 0; i < td_aux.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_total[i]['POWact_total']), d: td_aux[i]['date']});
					datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac_total[i]['POWrea_total']), d: td_aux[i]['date']});
				}
					aux_legend_tooltip = "Día: {d}, {name} Media: {y} kW";

			}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
				for (var i = 0; i < td_PotAct_total.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_total[i]['POWact_total']), z: td_aux[i]['eje']});
					datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac_total[i]['POWrea_total']), z: td_aux[i]['eje']});
				}
					aux_legend_tooltip = "{z}, {name} Media: {y} kW";
			}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
				for (var i = 0; i < td_PotAct_total.length; i++) {
					datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotAct_total[i]['POWact_total']), z:  td_aux[i]['eje']});
					datapoins2.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotReac_total[i]['POWrea_total']), z:  td_aux[i]['eje']});
				}
					aux_legend_tooltip = "{z}, {name} Media: {y} kW";
				}else{
				for (var i = 0; i < td_PotAct_total.length; i++) {
					datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotAct_total[i]['POWact_total']), d: td_aux[i]['date'], t: td_aux[i]['time']});
					datapoins2.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotReac_total[i]['POWrea_total']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				}
					aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} kW";
					aux_interval = null;
				}

	CanvasJS.addCultureInfo("es",
    {
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    });

	var chart1 = new CanvasJS.Chart("Potencia_avg_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Potencia Trifasica",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "PotenciaTrifasica-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisY: {
			suffix: " kW",
			includeZero: true,
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
			xValueType: "dateTime",
			valueFormatString: aux_valueFormatString,
			//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
			interval: aux_interval,
			intervalType: aux_intervaltype,
			titleFontColor: "#004165",
			// titleFontSize: 12,
			lineColor: "#004165",
			labelFontColor: "#004165",
			// interval: 30,
			labelAngle: 270+40,

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
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Potencia Activa",
			lineColor: "#004165",
			color: "#004165",

			legendMarkerColor: "#004165",
			dataPoints: datapoins1
		},
		{
			type: "spline",
			visible: true,
			showInLegend: true,
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Potencia Reactiva",
			lineColor: "#1E90FF",
			color: "#1E90FF",

			legendMarkerColor: "#1E90FF",
			dataPoints: datapoins2
		}/*,
		{
			type: "spline",
			visible: true,
			showInLegend: true,
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Potencia Activa L3",
			lineColor: "#004165",
			color: "#004165",

			legendMarkerColor: "#004165",
			dataPoints: datapoins3
		}
		*/
		]
	});

	chart1.render();

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		chart1.render();
	}



</script>

<script>

var td_PotAct_max = <?php echo json_encode($td_PotAct_max); ?>;
var datapoins1 = [];
var datapoins2 = [];
var datapoins3 = [];
var aux_legend_tooltip = "Hora: {z}, {name}: {y} kW";
var aux_interval = 1;
var aux_intervaltype =null;
var aux_valueFormatString = null;

		if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
			for (var i = 0; i < td_PotAct_max.length; i++) {
				datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotAct_max[i]['POWact1_max']), z: td_aux[i]['time']});
				datapoins2.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotAct_max[i]['POWact2_max']), z: td_aux[i]['time']});
				datapoins3.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotAct_max[i]['POWact3_max']), z: td_aux[i]['time']});
			}
				aux_valueFormatString = "HH:mm";
				aux_intervaltype = "hour";
				aux_interval = 1;

		}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
			for (var i = 0; i < td_PotAct_max.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact1_max']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact2_max']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact3_max']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
			}
				aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} kW";
				aux_interval = 288;
				aux_valueFormatString = "HH:mm";

		}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
			for (var i = 0; i < td_aux.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact1_max']), d: td_aux[i]['date']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact2_max']), d: td_aux[i]['date']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact3_max']), d: td_aux[i]['date']});
			}
				aux_legend_tooltip = "Día: {d}, {name} Máxima: {y} kW";

		}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
			for (var i = 0; i < td_PotAct_max.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact1_max']), z: td_aux[i]['eje']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact2_max']), z: td_aux[i]['eje']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact3_max']), z: td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Máxima: {y} kW";
		}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
			for (var i = 0; i < td_PotAct_max.length; i++) {
				datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact1_max']), z:  td_aux[i]['eje']});
				datapoins2.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact2_max']), z:  td_aux[i]['eje']});
				datapoins3.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotAct_max[i]['POWact3_max']), z:  td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Máxima: {y} kW";
			}else{
			for (var i = 0; i < td_PotAct_max.length; i++) {
				datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotAct_max[i]['POWact1_max']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotAct_max[i]['POWact2_max']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotAct_max[i]['POWact3_max']), d: td_aux[i]['date'], t: td_aux[i]['time']});
			}
				aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} kW";
				aux_interval = null;
			}

CanvasJS.addCultureInfo("es",
	{
		decimalSeparator: ",",// Observe ToolTip Number Format
		digitGroupSeparator: "."
	});

var chart2 = new CanvasJS.Chart("Potencia_max_"+contador, {
	theme: "light2",
	culture: "es",
	title:{
		text: "Potencia Activa por Fase",
		fontSize: 18,
		margin: 50,
		fontColor: "#004165"
	},
	exportFileName: "PotenciaActivaporFase-"+contador+"-"+date_from+"-"+date_to,
	exportEnabled: true,
	axisY: {
		suffix: " kW",
		includeZero: true,
		// minimum: 0,
		// valueFormatString:  "###0.##",
		titleFontColor: "#004165",
		lineColor: "#004165",
		labelFontColor: "#004165",
		tickColor: "#004165"
	},
	axisX: {
		// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
		xValueType: "dateTime",
		valueFormatString: aux_valueFormatString,
		//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
		interval: aux_interval,
		intervalType: aux_intervaltype,
		titleFontColor: "#004165",
		// titleFontSize: 12,
		lineColor: "#004165",
		labelFontColor: "#004165",
		// interval: 30,
		labelAngle: 270+40,

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
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Activa L1",
		lineColor: "#004165",
		color: "#004165",

		legendMarkerColor: "#004165",
		dataPoints: datapoins1
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Activa L2",
		lineColor: "#1E90FF",
		color: "#1E90FF",

		legendMarkerColor: "#1E90FF",
		dataPoints: datapoins2
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Activa L3",
		lineColor: "#800080",
		color: "#800080",

		legendMarkerColor: "#800080",
		dataPoints: datapoins3
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



var td_PotReac = <?php echo json_encode($td_PotReac); ?>;
var datapoins1 = [];
var datapoins2 = [];
var datapoins3 = [];
var aux_legend_tooltip = "Hora: {z}, {name}: {y} kVAr";
var aux_interval = 1;
var aux_intervaltype =null;
var aux_valueFormatString = null;

		if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
			for (var i = 0; i < td_PotReac.length; i++) {
				datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotReac[i]['POWrea1']), z: td_aux[i]['time']});
				datapoins2.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotReac[i]['POWrea2']), z: td_aux[i]['time']});
				datapoins3.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_PotReac[i]['POWrea3']), z: td_aux[i]['time']});
			}
				aux_valueFormatString = "HH:mm";
				aux_intervaltype = "hour";

		}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
			for (var i = 0; i < td_PotReac.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea1']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea2']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea3']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
			}
				aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} kVAr";
				aux_interval = 288;
				aux_valueFormatString = "HH:mm";

		}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
			for (var i = 0; i < td_aux.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea1']), d: td_aux[i]['date']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea2']), d: td_aux[i]['date']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea3']), d: td_aux[i]['date']});
			}
				aux_legend_tooltip = "Día: {d}, {name} Media: {y} kVAr";

		}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
			for (var i = 0; i < td_PotReac.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea1']), z: td_aux[i]['eje']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea2']), z: td_aux[i]['eje']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea3']), z: td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} kVAr";
		}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
			for (var i = 0; i < td_PotReac.length; i++) {
				datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea1']), z:  td_aux[i]['eje']});
				datapoins2.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea2']), z:  td_aux[i]['eje']});
				datapoins3.push({ label:  td_aux[i]['eje'], y: parseFloat(td_PotReac[i]['POWrea3']), z:  td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} kVAr";
			}else{
			for (var i = 0; i < td_PotReac.length; i++) {
				datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotReac[i]['POWrea1']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotReac[i]['POWrea2']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_PotReac[i]['POWrea3']), d: td_aux[i]['date'], t: td_aux[i]['time']});
			}
				aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} kVAr";
				aux_interval = null;
			}

CanvasJS.addCultureInfo("es",
	{
		decimalSeparator: ",",// Observe ToolTip Number Format
		digitGroupSeparator: "."
	});

var chart3 = new CanvasJS.Chart("Potencia_Reac_"+contador, {
	theme: "light2",
	culture: "es",
	title:{
		text: "Potencia Reactiva por Fase",
		fontSize: 18,
		margin: 50,
		fontColor: "#004165"
	},
	exportFileName: "PotenciaReactivaporFase-"+contador+"-"+date_from+"-"+date_to,
	exportEnabled: true,
	axisY: {
		suffix: " kVAr",
		includeZero: true,
		// minimum: 0,
		// valueFormatString:  "###0.##",
		titleFontColor: "#004165",
		lineColor: "#004165",
		labelFontColor: "#004165",
		tickColor: "#004165"
	},
	axisX: {
		// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
		xValueType: "dateTime",
		valueFormatString: aux_valueFormatString,
		//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
		interval: aux_interval,
		intervalType: aux_intervaltype,
		titleFontColor: "#004165",
		// titleFontSize: 12,
		lineColor: "#004165",
		labelFontColor: "#004165",
		// interval: 30,
		labelAngle: 270+40,

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
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Reactiva L1",
		lineColor: "#004165",
		color: "#004165",

		legendMarkerColor: "#004165",
		dataPoints: datapoins1
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Reactiva L2",
		lineColor: "#1E90FF",
		color: "#1E90FF",

		legendMarkerColor: "#1E90FF",
		dataPoints: datapoins2
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Potencia Reactiva L3",
		lineColor: "#800080",
		color: "#800080",

		legendMarkerColor: "#800080",
		dataPoints: datapoins3
	}
	]
});
chart3.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	chart3.render();
}

</script>

<script>


var td_Intensidad = <?php echo json_encode($td_Intensidad); ?>;
var datapoins1 = [];
var datapoins2 = [];
var datapoins3 = [];
var aux_legend_tooltip = "Hora: {z}, {name}: {y} A";
var aux_interval = 1;
var aux_intervaltype =null;
var aux_valueFormatString = null;

		if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
			for (var i = 0; i < td_Intensidad.length; i++) {
				datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_Intensidad[i]['IAC1']), z: td_aux[i]['time']});
				datapoins2.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_Intensidad[i]['IAC2']), z: td_aux[i]['time']});
				datapoins3.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_Intensidad[i]['IAC3']), z: td_aux[i]['time']});
			}
				aux_valueFormatString = "HH:mm";
				aux_intervaltype = "hour";

		}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
			for (var i = 0; i < td_Intensidad.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC1']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC2']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC3']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
			}
				aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} A";
				aux_interval = 288;
				aux_valueFormatString = "HH:mm";

		}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
			for (var i = 0; i < td_aux.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC1']), d: td_aux[i]['date']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC2']), d: td_aux[i]['date']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC3']), d: td_aux[i]['date']});
			}
				aux_legend_tooltip = "Día: {d}, {name} Media: {y} A";

		}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
			for (var i = 0; i < td_Intensidad.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC1']), z: td_aux[i]['eje']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC2']), z: td_aux[i]['eje']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC3']), z: td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} A";
		}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
			for (var i = 0; i < td_Intensidad.length; i++) {
				datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC1']), z:  td_aux[i]['eje']});
				datapoins2.push({ label:  td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC2']), z:  td_aux[i]['eje']});
				datapoins3.push({ label:  td_aux[i]['eje'], y: parseFloat(td_Intensidad[i]['IAC3']), z:  td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} A";
			}else{
			for (var i = 0; i < td_Intensidad.length; i++) {
				datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_Intensidad[i]['IAC1']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_Intensidad[i]['IAC2']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_Intensidad[i]['IAC3']), d: td_aux[i]['date'], t: td_aux[i]['time']});
			}
				aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} A";
				aux_interval = null;
			}

CanvasJS.addCultureInfo("es",
	{
		decimalSeparator: ",",// Observe ToolTip Number Format
		digitGroupSeparator: "."
	});

var chart4 = new CanvasJS.Chart("Corrientes_"+contador, {
	theme: "light2",
	culture: "es",
	title:{
		text: "Corrientes por Fase",
		fontSize: 18,
		margin: 50,
		fontColor: "#004165"
	},
	exportFileName: "CorrientesporFase-"+contador+"-"+date_from+"-"+date_to,
	exportEnabled: true,
	axisY: {
		suffix: " A",
		includeZero: true,
		// minimum: 0,
		// valueFormatString:  "###0.##",
		titleFontColor: "#004165",
		lineColor: "#004165",
		labelFontColor: "#004165",
		tickColor: "#004165"
	},
	axisX: {
		// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
		xValueType: "dateTime",
		valueFormatString: aux_valueFormatString,
		//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
		interval: aux_interval,
		intervalType: aux_intervaltype,
		titleFontColor: "#004165",
		// titleFontSize: 12,
		lineColor: "#004165",
		labelFontColor: "#004165",
		// interval: 30,
		labelAngle: 270+40,

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
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Corriente L1",
		lineColor: "#004165",
		color: "#004165",

		legendMarkerColor: "#004165",
		dataPoints: datapoins1
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Corriente L2",
		lineColor: "#1E90FF",
		color: "#1E90FF",

		legendMarkerColor: "#1E90FF",
		dataPoints: datapoins2
	},
	{
		type: "spline",
		visible: true,
		showInLegend: true,
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Corriente L3",
		lineColor: "#800080",
		color: "#800080",

		legendMarkerColor: "#800080",
		dataPoints: datapoins3
	}
	]
});
chart4.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	chart4.render();
}

</script>

<script>



var td_FDP = <?php echo json_encode($td_FDP); ?>;
var datapoins1 = [];
var datapoins2 = [];
var datapoins3 = [];
var aux_legend_tooltip = "Hora: {z}, {name}: {y} ";
var aux_interval = 1;
var aux_intervaltype =null;
var aux_valueFormatString = null;

		if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
			for (var i = 0; i < td_FDP.length; i++) {
				datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_FDP[i]['PF1']), z: td_aux[i]['time']});
				datapoins2.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_FDP[i]['PF2']), z: td_aux[i]['time']});
				datapoins3.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_FDP[i]['PF3']), z: td_aux[i]['time']});
			}
				aux_valueFormatString = "HH:mm";
				aux_intervaltype = "hour";

		}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
			for (var i = 0; i < td_FDP.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF1']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF2']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF3']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
			}
				aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} ";
				aux_interval = 288;
				aux_valueFormatString = "HH:mm";

		}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
			for (var i = 0; i < td_aux.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF1']), d: td_aux[i]['date']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF2']), d: td_aux[i]['date']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF3']), d: td_aux[i]['date']});
			}
				aux_legend_tooltip = "Día: {d}, {name} Media: {y} ";

		}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
			for (var i = 0; i < td_FDP.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF1']), z: td_aux[i]['eje']});
				datapoins2.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF2']), z: td_aux[i]['eje']});
				datapoins3.push({ label: td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF3']), z: td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} ";
		}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
			for (var i = 0; i < td_FDP.length; i++) {
				datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF1']), z:  td_aux[i]['eje']});
				datapoins2.push({ label:  td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF2']), z:  td_aux[i]['eje']});
				datapoins3.push({ label:  td_aux[i]['eje'], y: parseFloat(td_FDP[i]['PF3']), z:  td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} ";
			}else{
			for (var i = 0; i < td_FDP.length; i++) {
				datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_FDP[i]['PF1']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins2.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_FDP[i]['PF2']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				datapoins3.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_FDP[i]['PF3']), d: td_aux[i]['date'], t: td_aux[i]['time']});
			}
				aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} ";
				aux_interval = null;
			}

	CanvasJS.addCultureInfo("es",
    {
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    });

	var chart5 = new CanvasJS.Chart("FDP_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Factor de Potencia",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "FactordePotencia-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisY: {
			suffix: " ",
			includeZero: true,
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
			xValueType: "dateTime",
			valueFormatString: aux_valueFormatString,
			//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
			interval: aux_interval,
			intervalType: aux_intervaltype,
			titleFontColor: "#004165",
			// titleFontSize: 12,
			lineColor: "#004165",
			labelFontColor: "#004165",
			// interval: 30,
			labelAngle: 270+40,

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
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Factor L1",
			lineColor: "#004165",
			color: "#004165",
			legendMarkerColor: "#004165",
			dataPoints: datapoins1
		},
		{
			type: "spline",
			visible: true,
			showInLegend: true,
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Factor L2",
			lineColor: "#1E90FF",
			color: "#1E90FF",
			legendMarkerColor: "#1E90FF",
			dataPoints: datapoins2
		},
		{
			type: "spline",
			visible: true,
			showInLegend: true,
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Factor L3",
			lineColor: "#800080",
			color: "#800080",
			legendMarkerColor: "#800080",
			dataPoints: datapoins3
			//
		}
		]
	});
	chart5.render();

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		chart5.render();
	}

</script>

<script>


var td_COSPHI = <?php echo json_encode($td_COSPHI); ?>;
var datapoins1 = [];
var aux_legend_tooltip = "Hora: {z}, {name}: {y} ";
var aux_interval = 1;
var aux_intervaltype =null;
var aux_valueFormatString = null;

		if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
			for (var i = 0; i < td_COSPHI.length; i++) {
				datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_COSPHI[i]['COSPHI']), z: td_aux[i]['time']});
			}
				aux_valueFormatString = "HH:mm";
				aux_intervaltype = "hour";

		}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
			for (var i = 0; i < td_COSPHI.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_COSPHI[i]['COSPHI']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
			}
				aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} ";
				aux_interval = 288;
				aux_valueFormatString = "HH:mm";

		}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
			for (var i = 0; i < td_aux.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_COSPHI[i]['COSPHI']), d: td_aux[i]['date']});
			}
				aux_legend_tooltip = "Día: {d}, {name} Media: {y} ";

		}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
			for (var i = 0; i < td_COSPHI.length; i++) {
				datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_COSPHI[i]['COSPHI']), z: td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} ";
		}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
			for (var i = 0; i < td_COSPHI.length; i++) {
				datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_COSPHI[i]['COSPHI']), z:  td_aux[i]['eje']});
			}
				aux_legend_tooltip = "{z}, {name} Media: {y} ";
			}else{
			for (var i = 0; i < td_COSPHI.length; i++) {
				datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_COSPHI[i]['COSPHI']), d: td_aux[i]['date'], t: td_aux[i]['time']});
			}
				aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} ";
				aux_interval = null;
			}

CanvasJS.addCultureInfo("es",
	{
		decimalSeparator: ",",// Observe ToolTip Number Format
		digitGroupSeparator: "."
	});

var chart6 = new CanvasJS.Chart("Cosfi_"+contador, {
	theme: "light2",
	culture: "es",
	title:{
		text: "Coseno Φ",
		fontSize: 18,
		margin: 50,
		fontColor: "#004165"
	},
	exportFileName: "CosenoΦ-"+contador+"-"+date_from+"-"+date_to,
	exportEnabled: true,
	axisY: {
		suffix: " ",
		includeZero: false,
		// minimum: 0,
		// valueFormatString:  "###0.##",
		titleFontColor: "#004165",
		lineColor: "#004165",
		labelFontColor: "#004165",
		tickColor: "#004165"
	},
	axisX: {
		// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
		xValueType: "dateTime",
		valueFormatString: aux_valueFormatString,
		//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
		interval: aux_interval,
		intervalType: aux_intervaltype,
		titleFontColor: "#004165",
		// titleFontSize: 12,
		lineColor: "#004165",
		labelFontColor: "#004165",
		// interval: 30,
		labelAngle: 270+40,

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
		toolTipContent: aux_legend_tooltip,
		// yValueFormatString: "###0.## kg CO2 eq",
		name: "Coseno Φ",
		lineColor: "#004165",
		color: "#004165",

		legendMarkerColor: "#004165",
		dataPoints: datapoins1
	}
	]
});
chart6.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	chart6.render();
}

</script>

<script>


	var td_Frecuencia = <?php echo json_encode($td_Frecuencia); ?>;
	var datapoins1 = [];
	var aux_legend_tooltip = "Hora: {z}, {name}: {y} hz";
	var aux_interval = 1;
	var aux_intervaltype =null;
	var aux_valueFormatString = null;

			if(label_intervalo == "Ayer" || label_intervalo == "Hoy"){
				for (var i = 0; i < td_Frecuencia.length; i++) {
					datapoins1.push({ x: new Date("1970-01-05T"+td_aux[i]['time']+":00"), y: parseFloat(td_Frecuencia[i]['FRE']), z: td_aux[i]['time']});
				}
					aux_valueFormatString = "HH:mm";
					aux_intervaltype = "hour";

			}else if(label_intervalo == "Semana Actual" || label_intervalo == "Semana Anterior"){
				for (var i = 0; i < td_Frecuencia.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Frecuencia[i]['FRE']), d: td_aux[i]['date'] , t: td_aux[i]['time']});
				}
					aux_legend_tooltip ="{label}({d}) Hora: {t} - {name}: {y} Hz";
					aux_interval = 288;
					aux_valueFormatString = "HH:mm";

			}else if(label_intervalo == "Mes Anterior" || label_intervalo == "Mes Actual"){
				for (var i = 0; i < td_aux.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Frecuencia[i]['FRE']), d: td_aux[i]['date']});
				}
					aux_legend_tooltip = "Día: {d}, {name} Media: {y} hz";

			}else if(label_intervalo == "Trimestre Actual" || label_intervalo == "Ultimo Trimestre"){
				for (var i = 0; i < td_Frecuencia.length; i++) {
					datapoins1.push({ label: td_aux[i]['eje'], y: parseFloat(td_Frecuencia[i]['FRE']), z: td_aux[i]['eje']});
				}
					aux_legend_tooltip = "{z}, {name} Media: {y} hz";
			}else if(label_intervalo == "Año Actual" || label_intervalo == "Último Año"){
				for (var i = 0; i < td_Frecuencia.length; i++) {
					datapoins1.push({ label:  td_aux[i]['eje'], y: parseFloat(td_Frecuencia[i]['FRE']), z:  td_aux[i]['eje']});
				}
					aux_legend_tooltip = "{z}, {name} Media: {y} hz";
				}else{
				for (var i = 0; i < td_Frecuencia.length; i++) {
					datapoins1.push({ label: td_aux[i]['date']+" "+td_aux[i]['time'], y: parseFloat(td_Frecuencia[i]['FRE']), d: td_aux[i]['date'], t: td_aux[i]['time']});
				}
					aux_legend_tooltip = "Día: {d} Hora: {t}, {name}: {y} hz";
					aux_interval = null;
				}

	CanvasJS.addCultureInfo("es",
    {
      decimalSeparator: ",",// Observe ToolTip Number Format
      digitGroupSeparator: "."
    });

	var chart7 = new CanvasJS.Chart("Frecuencia_"+contador, {
		theme: "light2",
		culture: "es",
		title:{
			text: "Frecuencia",
			fontSize: 18,
			margin: 50,
			fontColor: "#004165"
		},
		exportFileName: "Frecuencia-"+contador+"-"+date_from+"-"+date_to,
		exportEnabled: true,
		axisY: {
			suffix: " Hz",
			includeZero: false,
			// minimum: 0,
			// valueFormatString:  "###0.##",
			titleFontColor: "#004165",
			lineColor: "#004165",
			labelFontColor: "#004165",
			tickColor: "#004165"
		},
		axisX: {
			// title: "Total Emisiones CO2: "+ totalEmi +"kg CO2 eq",
			xValueType: "dateTime",
			valueFormatString: aux_valueFormatString,
			//labelFormatter: function (e) {return CanvasJS.formatDate( e.value, "HH:mm");},
			interval: aux_interval,
			intervalType: aux_intervaltype,
			titleFontColor: "#004165",
			// titleFontSize: 12,
			lineColor: "#004165",
			labelFontColor: "#004165",
			// interval: 30,
			labelAngle: 270+40,

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
			toolTipContent: aux_legend_tooltip,
			// yValueFormatString: "###0.## kg CO2 eq",
			name: "Frecuencia",
			lineColor: "#004165",
			color: "#004165",

			legendMarkerColor: "#004165",
			dataPoints: datapoins1
		}
		]
	});
	chart7.render();

	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		chart7.render();
	}
</script>

<script>
//clicks

		function show1() {
			document.getElementById("gra_1_1").style='display:block;';
			document.getElementById("gra_1_2").style='display:none;';
			document.getElementById("gra_1_3").style='display:none;';
			chart1.render();
				}

		function show2() {
			document.getElementById("gra_1_1").style='display:none';
			document.getElementById("gra_1_2").style='display:block;';
			document.getElementById("gra_1_3").style='display:none;';
			chart2.render();
		    }

		function show3() {
			document.getElementById("gra_1_1").style='display:none';
			document.getElementById("gra_1_2").style='display:none;';
			document.getElementById("gra_1_3").style='display:block;';
			chart3.render();
		    }

		function show4() {
			document.getElementById("gra_2_1").style='display:block;';
			document.getElementById("gra_2_2").style='display:none;';
			document.getElementById("gra_2_3").style='display:none;';
			document.getElementById("gra_2_4").style='display:none;';
			chart4.render();
		    }

		function show5() {
			document.getElementById("gra_2_1").style='display:none;';
			document.getElementById("gra_2_2").style='display:block;';
			document.getElementById("gra_2_3").style='display:none;';
			document.getElementById("gra_2_4").style='display:none;';
			chart5.render();
		    }

		function show6() {
			document.getElementById("gra_2_1").style='display:none;';
			document.getElementById("gra_2_2").style='display:none;';
			document.getElementById("gra_2_3").style='display:block;';
			document.getElementById("gra_2_4").style='display:none;';
			chart6.render();
		    }

		function show7() {
			document.getElementById("gra_2_1").style='display:none;';
			document.getElementById("gra_2_2").style='display:none;';
			document.getElementById("gra_2_3").style='display:none;';
			document.getElementById("gra_2_4").style='display:block;';
			chart7.render();
		    }
</script>

<script>




	var email = <?php echo json_encode($user->email) ?>;
	var date_from = <?php echo json_encode($date_from) ?>;
	var date_to = <?php echo json_encode($date_to) ?>;
	var conta = <?php echo json_encode($contador_label) ?>;
	var analizador = <?php echo json_encode($analizador->label) ?>;
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


		var cntChart = $(".plot-tab");
		var handleCharts = [];
		var dataCharts = [];

		var idxElement = 1;
		var width = parseInt($("#Potencia_avg_"+contador+" .canvasjs-chart-canvas").width());

		chart1.options.width = 1000;
		chart2.options.width = 1000;
		chart3.options.width = 1000;
		chart4.options.width = 1000;
		chart5.options.width = 1000;
		chart6.options.width = 1000;
		chart7.options.width = 1000;

		chart1.render();
		chart2.render();
		chart3.render();
		chart4.render();
		chart5.render();
		chart6.render();
		chart7.render();

		var canvas = $("#Potencia_avg_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#Potencia_max_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#Potencia_Reac_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#Corrientes_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#FDP_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#Cosfi_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		var canvas = $("#Frecuencia_"+contador+" .canvasjs-chart-canvas").get(0);
		var data = canvas.toDataURL('image/jpeg', 1.0);
		dataCharts.push(data);

		delete chart1.options.width;
		delete chart2.options.width;
		delete chart3.options.width;
		delete chart4.options.width;
		delete chart5.options.width;
		delete chart6.options.width;
		delete chart7.options.width;

		chart1.render();
		chart2.render();
		chart3.render();
		chart4.render();
		chart5.render();
		chart6.render();
		chart7.render();

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
		for(var i = 0; i < 5; i++) {
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
