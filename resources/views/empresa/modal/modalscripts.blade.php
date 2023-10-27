<script>
	var assignCounter = function(row) {
		var data = $("#dtAssignEnergy").DataTable().rows().data();
		banAdd = true;
		for(var i = 0; i < data.length; i++) {
			if(data[i][0] == row[0]) {
				banAdd = false;
				idxUpdate = i;
				break;
			}
		}
		if(banAdd) {
			$("#dtAssignEnergy").DataTable().row.add(row).draw(false);
		}
		else {
			$("#dtAssignEnergy").DataTable().cell(idxUpdate, 1).data(row[1]).draw(false);
		}
	}
	
	var assignAnalyzerDt = function(row) {
		var data = $("#dtAssignAnalyzer").DataTable().rows().data();
		banAdd = true;
		for(var i = 0; i < data.length; i++) {
			if(data[i][0] == row[0]) {
				banAdd = false;
				idxUpdate = i;
				break;
			}
		}
		if(banAdd) {
			$("#dtAssignAnalyzer").DataTable().row.add(row).draw(false);
		}
		else {
			$("#dtAssignAnalyzer").DataTable().cell(idxUpdate, 1).data(row[1]);
			$("#dtAssignAnalyzer").DataTable().cell(idxUpdate, 2).data(row[2]);
		}
	}
	
	var assignUserDt = function(row) {
		var data = $("#dtAssignUser").DataTable().rows().data();
		banAdd = true;
		for(var i = 0; i < data.length; i++) {
			if(data[i][0] == row[0]) {
				banAdd = false;
				idxUpdate = i;
				break;
			}
		}
		if(banAdd) {
			$("#dtAssignUser").DataTable().row.add(row).draw(false);
		}
		else {
			$("#dtAssignUser").DataTable().cell(idxUpdate, 1).data(row[1]);
			$("#dtAssignUser").DataTable().cell(idxUpdate, 2).data(row[2]);
		}
	}
	
	var removeAssignEnergy = function(event){
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignEnergy').DataTable().row(rowObject[0]);
			row.remove().draw(false);
		}
	};
	
	var removeAssignAnalyzer = function(event){
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignAnalyzer').DataTable().row(rowObject[0]);
			row.remove().draw(false);
		}
	};
	
	var removeAssignUser = function(event){
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignUser').DataTable().row(rowObject[0]);
			row.remove().draw(false);
		}
	};
	
	var showGroupData = function(event) {
		var init = $("#modalUserGroup").data("init");
		init();
	}
	
	var assignEnergyUser = function(event) {
		event.preventDefault();
		var rows = $("#dtAssignEnergy").DataTable().rows().data();
		var energy = [];
		for(var i = 0; i < rows.length; i++) {
			energy.push([rows[i][0], rows[i][1]]);
		}
	
		var rows = $("#dtAssignAnalyzer").DataTable().rows().data();
		// console.log(rows);
		var analyzers = [];
		for(var i = 0; i < rows.length; i++) {
			analyzers.push([rows[i][0], rows[i][1]]);
		}
	
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignUser').DataTable().row(rowObject[0]);
			var data = row.data();
	
			var iptUserEnergy = $("#iptUserEnergy input[name='userEnergy[" + data[0] + "][]']");
			var userEnergy = [];
			for(var i = 0; i < iptUserEnergy.length; i++) {
				userEnergy.push($(iptUserEnergy[i]).val());
			}
	
			var iptUserAnalyzer = $("#iptUserAnalyzer input[name='userAnalyzer[" + data[0] + "][]']");
			var userAnalyzers = [];
			for(var i = 0; i < iptUserAnalyzer.length; i++) {
				userAnalyzers.push($(iptUserAnalyzer[i]).val());
			}
			
			var init = $("#modalUserEnergy").data("init");
			$("#modalUserEnergy").data("energy", energy);
			$("#modalUserEnergy").data("analyzers", analyzers);
			$("#modalUserEnergy").data("user", data[0]);
			$("#modalUserEnergy").data("rowIdx", row.index());
			$("#modalUserEnergy").data("userEnergy", userEnergy);
			$("#modalUserEnergy").data("userAnalyzers", userAnalyzers);
			init();
			$("#modalUserEnergy").data("save", saveDataFormUserEnergy)
			$("#modalUserEnergy").modal("show");
		}	
	}
	
	var assignGroupUser = function(event) {
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignUser').DataTable().row(rowObject[0]);
			var data = row.data();
	
			var ipt = $("#iptUserGroup input[name^='userGroup[" + data[0] + "]']");
			var userGroup = [];
			if(ipt.length > 0) {
				for(var i = 0; i < ipt.length; i++) {
					var energy = $(ipt[i]).data("energy");
					if(energy != undefined) {
						userGroup[energy] = $(ipt[i]).val();
					}
				}
			}
	
			var rows = $('#dtAssignEnergy').DataTable().rows().data();
			var energyData = [];
			for(var i = 0; i< rows.length; i++) {
				energyData.push([rows[i][0], rows[i][1]]);
			}
			
			var init = $("#modalGroupUser").data("init");
			$("#modalGroupUser").data("user", data[0]);
			$("#modalGroupUser").data("energy", energyData);
			$("#modalGroupUser").data("rowIdx", row.index());
			$("#modalGroupUser").data("groups", userGroup);
			$("#modalGroupUser").data("save", saveDataFormUserGroup);
			init();
		}
	}
	
	var saveDataFormUserEnergy = function() {
		var userEnergy = $("#modalUserEnergy").data("userEnergy");
		var user = $("#modalUserEnergy").data("user");
		var rowIdx = $("#modalUserEnergy").data("rowIdx");
		var ipts = $("#iptUserEnergy input[name='userEnergy[" + user + "][]']").remove();
		for(var i = 0; i < userEnergy.length; i++) {
			var ipt = $("<input/>");
			ipt.attr("type", "hidden");
			ipt.attr("name", "userEnergy[" + user + "][]");
			ipt.attr("value", userEnergy[i]);
			$("#iptUserEnergy").append(ipt);
		}
	
		var userAnalyzer = $("#modalUserEnergy").data("userAnalyzer");
		var ipts = $("#iptUserAnalyzer input[name='userAnalyzer[" + user + "][]']").remove();
		for(var i = 0; i < userAnalyzer.length; i++) {
			var ipt = $("<input/>");
			ipt.attr("type", "hidden");
			ipt.attr("name", "userAnalyzer[" + user + "][]");
			ipt.attr("value", userAnalyzer[i]);
			$("#iptUserAnalyzer").append(ipt);
		}
		
		$('#dtAssignUser').DataTable().cell(rowIdx, 4).data("Contadores: " + userEnergy.length + "<br/> Analizadores:" + userAnalyzer.length);
		$("#modalUserEnergy").modal("hide");
	}
	
	var saveDataFormUserGroup = function() {
		var groups = $("#modalGroupUser").data("groups");
		var energy = $("#modalGroupUser").data("energy");
		var user = $("#modalGroupUser").data("user");
		var rowIdx = $("#modalGroupUser").data("rowIdx");
		$("#iptUserGroup input[name^='userGroup[" + user + "]']").remove();
	
		for(var i = 0; i< energy.length; i++) {
			var nIpt = $("<input/>");
			nIpt.attr("type", "hidden");
			nIpt.attr("name", "userGroup[" + user + "][" + energy[i] + "]");
			nIpt.attr("value", groups[i]);
			$("#iptUserGroup").append(nIpt);
			nIpt.data("energy", energy[i]);
		}
		
		$('#dtAssignUser').DataTable().cell(rowIdx, 3).data(groups.length);
		$("#modalGroupUser").modal("hide");
	}
	
	var saveDataFormAnalyzerGroups = function() {
		var aGroups = $("#modalGroupsAnalyzers").data("aGroups");
		$("#iptGroupAnalyzer input[name^='analyzerGroups[]']").remove();
	
		for(var i = 0; i< aGroups.length; i++) {
			var nIpt = $("<input/>");
			nIpt.attr("type", "hidden");
			nIpt.attr("name", "analyzerGroups[]");
			nIpt.attr("value", aGroups[i]);
			$("#iptGroupAnalyzer").append(nIpt);
		}
		
		$("#modalGroupsAnalyzers").modal("hide");
	}
	
	var editAssignEnergy = function(event) {
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignEnergy').DataTable().row(rowObject[0]);
			var data = row.data();
			if($.isNumeric(data[0])) {
				openEnergyModal();
				var edit = $("#modalEnergyMeter").data("edit");
					if(edit) {
						edit(data[0]);    			
					}
			}		
		}
	}
	
	var editAssignAnalyzer = function(event) {
		event.preventDefault();
		var rowObject = $(this).closest("tr");
		if(rowObject.length > 0) {
			row =  $('#dtAssignAnalyzer').DataTable().row(rowObject[0]);
			var data = row.data();		
			if($.isNumeric(data[0])) {
				openAnalyzerModal();
				var edit = $("#modalAnalyzer").data("edit");
					if(edit) {
						edit(data[0]);
					}			
			}
		}
	}
	
	var initAssignEnergyDt = function() {
		$("#dtAssignEnergy").dataTable({
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			},
			"autoWidth": false,
			"columnDefs": [
				{
					"targets": [ 0 ],
					"visible": false,
					"searchable": false
				},
				{
					"targets": [ 2, 3],
					"visible": true,
					"searchable": false,
					"sortable": false
				},
				{
					"targets": [ 2 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnEdit").html();
						html = $(html);
						html.addClass("btn-edit-assign-energy");
						return html[0].outerHTML;
					}
				},
				{
					"targets": [ 3 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnDelete").html();
						html = $(html);
						html.addClass("btn-delete-assign-energy");
						return html[0].outerHTML;
					}
				}
			],
			"drawCallback": function( settings ) {
				$(".btn-edit-assign-energy").off("click").click(editAssignEnergy);
				$(".btn-delete-assign-energy").off("click").click(removeAssignEnergy);
			}
		});
	}
	
	var initAssignAnalyzerDt = function() {
		$("#dtAssignAnalyzer").dataTable({
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			},
			"autoWidth": false,
			"columnDefs": [
				{
					"targets": [0],
					"visible": false,
					"searchable": false
				},
				{
					"targets": [1, 2],
					"visible": true,
					"searchable": true
				},
				{
					"targets": [3, 4],
					"visible": true,
					"searchable": false,
					"sortable": false
				},
				{
					"targets": [3],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnEdit").html();
						html = $(html);
						html.addClass("btn-edit-assign-analyzer");
						return html[0].outerHTML;
					}
				},
				{
					"targets": [ 4 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnDelete").html();
						html = $(html);
						html.addClass("btn-delete-assign-analyzer");
						return html[0].outerHTML;
					}
				}
			],
			"drawCallback": function( settings ) {
				$(".btn-edit-assign-analyzer").off("click").click(editAssignAnalyzer);
				$(".btn-delete-assign-analyzer").off("click").click(removeAssignAnalyzer);
			}
		});
	}
	
	var initAssignUserDt = function() {
		$("#dtAssignUser").dataTable({
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			},
			"autoWidth": false,
			"columnDefs": [
				{
					"targets": [ 0 ],
					"visible": false,
					"searchable": false
				},
				{
					"targets": [ 5, 6, 7],
					"visible": true,
					"searchable": false,
					"sortable": false
				},
				{
					"targets": [ 5 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnUserEnergy").html();
						html = $(html);
						html.addClass("btn-assign-energy-user");
						return html[0].outerHTML;
					}
				},
				{
					"targets": [ 6 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnUserGroup").html();
						html = $(html);
						html.addClass("btn-assign-group-user");
						return html[0].outerHTML;
					}
				},
				{
					"targets": [ 7 ],                
					"render": function ( data, type, row ) {
						var html = $("#templateBtnDelete").html();
						html = $(html);
						html.addClass("btn-delete-assign-user");
						return html[0].outerHTML;
					}
				}
			],
			"drawCallback": function( settings ) {
				$(".btn-delete-assign-user").off("click").click(removeAssignUser);
				$(".btn-assign-energy-user").off("click").click(assignEnergyUser);
				$(".btn-assign-group-user").off("click").click(assignGroupUser);
			}
		});
	}
	
	var updateInputEnergy = function() {
		$("#iptEnergy").html("");
		var rows = $("#dtAssignEnergy").DataTable().rows().data();
		for(var i = 0; i < rows.length; i++) {
			var ipt = $("<input />");
			ipt.prop("name", "meters[]");
			ipt.attr("value", rows[i][0]);
			$("#iptEnergy").append(ipt);
		}
	}
	
	var updateInputAnalyzer = function() {
		$("#iptAnalyzer").html("");
		var rows = $("#dtAssignAnalyzer").DataTable().rows().data();
		for(var i = 0; i < rows.length; i++) {
			var ipt = $("<input />");
			ipt.prop("name", "analyzers[]");
			ipt.attr("value", rows[i][0]);
			$("#iptAnalyzer").append(ipt);
		}
	}
	
	var updateInputUser = function() {
		$("#iptUser").html("");
		var rows = $("#dtAssignUser").DataTable().rows().data();
		for(var i = 0; i < rows.length; i++) {
			var ipt = $("<input />");
			ipt.prop("name", "users[]");
			ipt.attr("value", rows[i][0]);
			$("#iptUser").append(ipt);
		}
	}
	
	var openEnergyModal = function(){
		var data = $("#dtAssignEnergy").DataTable().rows().data();
		var assigned = [];	
		for(var i = 0; i < data.length; i++) {
			assigned.push(parseInt(data[i][0]));
		}
		$("#modalEnergyMeter").data("assigned", assigned);
		$("#modalEnergyMeter").data("assign", assignCounter);
		var reset = $("#modalEnergyMeter").data("reset");
		reset();
	}
	
	var openAnalyzerModal = function() {
		var data = $("#dtAssignAnalyzer").DataTable().rows().data();
		var assigned = [];	
		for(var i = 0; i < data.length; i++) {
			assigned.push(parseInt(data[i][0]));
		}
		$("#modalAnalyzer").data("assigned", assigned);
		$("#modalAnalyzer").data("assign", assignAnalyzerDt);
		var reset = $("#modalAnalyzer").data("reset");
		reset();
	}
	
	var openGroupAnalyzerModal = function() {
		var iptGroups = $("#iptGroupAnalyzer input[name^='analyzerGroup']");
	
		var dataGroups = [];
		for(var i = 0; i < iptGroups.length; i++) {
			dataGroups.push($(iptGroups[i]).val());
		}
		$("#modalGroupsAnalyzers").data("assigned", dataGroups);
		$("#modalGroupsAnalyzers").data("saveDataF", saveDataFormAnalyzerGroups);
		var reset = $("#modalGroupsAnalyzers").data("reset");
		reset();
	}
	
	
	$(document).ready(function(){
		$("#btnContadores").click(function(){
			openEnergyModal();
		});
	
		$("#btnAnalyzers").click(function(){
			openAnalyzerModal();
		});
	
		$("#btnGroupsAnalyzers").click(function(){
			openGroupAnalyzerModal();
		});
	
		$("#btnUsers").click(function(){
			var data = $("#dtAssignUser").DataTable().rows().data();
			var assigned = [];	
			for(var i = 0; i < data.length; i++) {
				assigned.push(parseInt(data[i][0]));
			}
			$("#modalUser").data("assigned", assigned);
			$("#modalUser").data("assign", assignUserDt);
			var reset = $("#modalUser").data("reset");
			reset();
		});
	
		$("#dtAssignEnergy").on("draw.dt", function(event) {
			updateInputEnergy();		
		});
	
		$("#dtAssignAnalyzer").on("draw.dt", function(event) {
			updateInputAnalyzer();		
		});
	
		$("#dtAssignUser").on("draw.dt", function(event) {
			updateInputUser();		
		});
	
		$("#btnGroups").click(function(event){
			event.preventDefault();
			showGroupData();
		});
		
		initAssignEnergyDt();
		initAssignAnalyzerDt();
		initAssignUserDt();
	});
</script>
	