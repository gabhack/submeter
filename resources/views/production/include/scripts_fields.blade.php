<script type="text/javascript">
<!--
	var url_return = "{{ $url_return }}"

	var urlFieldsDatabase = "{{ route('production.database') }}";
    var initFieldsDt = function() {
    	$("#dtFields").dataTable({
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
                    "targets": [ 3 ],                
                    "render": function ( data, type, row ) {
                        var html = $("#templateBtnEdit").html();
                        html = $(html);
                        html.addClass("btn-edit-field");
                        return html[0].outerHTML;
                    }
                },
                {
                    "targets": [ 4 ],                
                    "render": function ( data, type, row ) {
                        var html = $("#templateBtnDelete").html();
                        html = $(html);
                        html.addClass("btn-delete-field");
                        return html[0].outerHTML;
                    }
                }
            ],
            "drawCallback": function( settings ) {
            	$(".btn-edit-field").off("click").click(editField);
                $(".btn-delete-field").off("click").click(removeField);
            }
    	});
    }

    var initFieldsGroupDt = function() {
    	$("#dtFieldsGroup").dataTable({
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
                    "targets": [ 5 ],                
                    "render": function ( data, type, row ) {
                        var html = $("#templateBtnEdit").html();
                        html = $(html);
                        html.addClass("btn-edit-fieldg");
                        return html[0].outerHTML;
                    }
                },
                {
                    "targets": [ 6 ],                
                    "render": function ( data, type, row ) {
                        var html = $("#templateBtnDelete").html();
                        html = $(html);
                        html.addClass("btn-delete-fieldg");
                        return html[0].outerHTML;
                    }
                }
            ],
            "drawCallback": function( settings ) {
            	$(".btn-edit-fieldg").off("click").click(editFieldGroup);
                $(".btn-delete-fieldg").off("click").click(removeFieldGroup);
            }
    	});
    }

	$(document).ready(function(){
		$("#btnAddField").click(addField);
		$("#btnAddFieldGroup").click(addFieldGroup);
		$("#btnAddOperandField").click(addOperandEvent);		
		$("#btnAddOperandFieldGroup").click(addOperandGroupEvent);
		$(".sel-name-ch").change(changeSelName);
		$("#fieldFormatNumber").change(changeFormatField);
		$("#fieldFormatGroupNumber").change(changeFormatFieldGroup);
		$("#btnSaveField").click(saveFieldData);
		$("#btnSaveFieldGroup").click(saveFieldDataGroup);
		//$("#btnLoadDatabase").click(readFieldsDatabaseEvent); @Leo W ya no se va a usar
		$(".sel-operand-type").change(eventOperantType);
		$("[name='database']").change(selectDatabaseEvent);
		$("[name='table']").change(selectTableEvent);

		$("[name='enterprise']").change(loadCounters);
		
		$("#form-fields").submit(saveDataFields);
		//readFieldsDatabase(false);
		initFieldsDt();
		initFieldsGroupDt();
		loadCounters();
		
		//loadDatabaseInfo();
		$('select[name="energymeter"]').change(function(){
			loadConnectionsInfo()
		});

		$(document).on('change',"select[name='field_database[]']",function(e){
			var container = $(this).parent().parent();
			loadTables(container,'field');
			container.find("select[name='field_table[]']").trigger( "change" );
		});

		$(document).on('change',"select[name='group_database[]']",function(e){
			var container = $(this).parent().parent();
			loadTables(container,'group');
			container.find("select[name='group_table[]']").trigger( "change" );
		});


		$(document).on('change',"select[name='field_table[]']",function(e){
			var container = $(this).parent().parent();
			loadFields(container,'field');
		});

		$(document).on('change',"select[name='group_table[]']",function(e){
			var container = $(this).parent().parent();
			loadFields(container,'group');
		});

		$(document).on('change',"select[name='operand_type[]']",function(e){
			var container = $(this).parent().parent().parent();
			container.find("select[name='field_database[]']").attr('disabled','disabled');
			container.find("select[name='field_table[]']").attr('disabled','disabled');
			if($(this).val() == 2){
				container.find("select[name='field_database[]']").removeAttr('disabled');
				container.find("select[name='field_table[]']").removeAttr('disabled');
				container.find("select[name='field_database[]']").trigger( "change" );
			}
		});

		$(document).on('change',"select[name='operandgroup_type[]']",function(e){
			var container = $(this).parent().parent().parent();
			container.find("select[name='group_database[]']").attr('disabled','disabled');
			container.find("select[name='group_table[]']").attr('disabled','disabled');
			if($(this).val() == 2){
				container.find("select[name='group_database[]']").removeAttr('disabled');
				container.find("select[name='group_table[]']").removeAttr('disabled');
				container.find("select[name='group_database[]']").trigger( "change" );
			}
		});
		
		
		
	});

	var loadCounters = function(){
		$('select[name="energymeter"]').html('');
		$.ajax({
	 		type: "GET",
	 		url: '/produccion/counters/'+$('select[name="enterprise"]').val(),
		    dataType: "json",
	        success: function (response) {
	            if(response.error) {
					return false;
	            }
				for (let index = 0; index < response.length; index++) 
				{
					const element = response[index];
					var selected = $('select[name="energymeter"]').attr('data-value') == element.id ? 'selected="selected"':'';
					$('select[name="energymeter"]').append($('<option value="'+element.id+'" '+selected+'>'+element.name+'</option>'));
				}
				loadConnectionsInfo();
	        },
	        error: function (data) {
	            console.log(data);
	        }	            
	    });
	}

    var loadTables = function(container,operand_type){
		container.find("select[name='"+operand_type+"_table[]']").empty();
		var dbid = container.find("select[name='"+operand_type+"_database[]']").val();
		const tls = window.databases.find(function(r){
			return r.id == dbid;
		});
		
		if(tls)
		{
			for (let index = 0; index < tls.tables.length; index++) 
			{
				const tl = tls.tables[index];
				var option = $('<option></option>').attr("value", tl.name).text(tl.name);
				container.find("select[name='"+operand_type+"_table[]']").append(option);
			}
		}
		
	};

	var loadFields = function (container,operand_type) {
		var value_type = 'value_table';
		if(operand_type == 'group') value_type = 'valuegroup_table';
		
		container.find("select[name='"+value_type+"[]']").empty();
		
		var dbid = container.find("select[name='"+operand_type+"_database[]']").val();
		var tbName = container.find("select[name='"+operand_type+"_table[]']").val();
		const tls = window.databases.find(function(r){
			return r.id == dbid;
		});
		if(tls)
		{
			const fls = tls.tables.find(function(r){
				return r.name == tbName;
			});
			
			if(fls)
			{
				for (let index = 0; index < fls.fields.length; index++) {
					const fl = fls.fields[index];
					var option = $('<option></option>').attr("value", fl.name).text(fl.name);
					container.find("select[name='"+value_type+"[]']").append(option);
				}
			}
		}
	};

	var loadConnectionsInfo = function(){ //Leo W obtener base de datos/tablas/campos del contador seleccionado
		var counter = $('select[name="energymeter"]').val();
		$.ajax({
	 		type: "GET",
	 		url: '/produccion/connections/'+counter,
		    dataType: "json",
	        success: function (response) {
	            if(response.error) {
					return false;
	            }
	            window.databases = response;
				console.log(window.databases);
	        },
	        error: function (data) {
	            console.log(data);
	        }	            
	    });
	}

	var selectDatabaseEvent = function(event) {
		console.log('Base de datos seleccionada');
		$("input[name='selecteddatabase']").val($("[name='database']").val());
		var database_data = $("#btnLoadDatabase").data("databases_data");
		var database_name = $("input[name='selecteddatabase']").val();
		var database;
		for(var i = 0; i < database_data.length; i++) {
			if(database_data[i].name == database_name) {
				database = database_data[i];
				break;
			}
		}		

		if(database) {
			loadTableData(database);
		}	
	}

	var selectTableEvent = function(event) {
		console.log('Tabla seleccionada');
		$("input[name='selectedtable']").val($("[name='table']").val());

		var database_data = $("#btnLoadDatabase").data("databases_data");
		var database_name = $("input[name='selecteddatabase']").val();
		var database;
		for(var i = 0; i < database_data.length; i++) {
			if(database_data[i].name == database_name) {
				database = database_data[i];
				break;
			}
		}		

		if(database) {
			loadFieldData(database);
		}
		
	}

	var readFieldsDatabaseEvent = function(event) {
		if(event) {
			event.preventDefault();
		}
		//readFieldsDatabase(true); Leo W ya no se va a usar
	}

	var readFieldsDatabase  = function(cleanDT) {
		var fields = $(".field-d");
		var data = {};
		for(var i = 0; i < fields.length; i++) {
			var ipt = $(fields[i]);
			var val = ipt.val();
			if(val.length == 0) {
				return false;
			}
			data[ipt.attr("name")] = val;
		}
		$.ajax({
	 		type: "GET",
	 		url: urlFieldsDatabase,
		    dataType: "json",
		    data: data,
	        success: function (data) {
	            if(data.error) {
					return false;
	            }
	            if(cleanDT) {
	            	$("#dtFields").DataTable().clear().draw(false);
	            	$("#dtFieldsGroup").DataTable().clear().draw(false);
	            }

	            $("[name='database']").html("");	            

	            var database, selDatabase;
	            var selected_database = $("input[name='selecteddatabase']").val();
	            for(var i = 0; i < data.databases.length; i++) {
					if(!database) {
						database = data.databases[i];
					}
					if(selected_database.length > 0) {
						if(selected_database == data.databases[i].name) {
							selDatabase = data.databases[i];
						}
					}
					var opt = $("<option />");
					opt.attr("value", data.databases[i].name);
					opt.html(data.databases[i].name + ' ('+ data.databases[i].count_label +')');
					$("[name='database']").append(opt);
	            }
				if(database) {
    
    				if(selected_database.length > 0) {
        				if($("[name='database']").find("option[value='" + selected_database + "']").length > 0) {
							$("[name='database']").val(selected_database);
							database = selDatabase;
						}    					
    				}
    
    				$("input[name='selecteddatabase']").val($("[name='database']").val());

    				loadTableData(database);
    				$("#btnLoadDatabase").data("databases_data", data.databases);
				}
	        },
	        error: function (data) {
	            console.log(data);
	        }	            
	    });
	}

	var loadSelectFields = function(field_name) {
		if(!field_name) {
			field_name = "-1";
		}
		var dt = $("#dtFields").DataTable();
		var data = dt.rows().data();
		var fields = [];
		for(var i = 0; i < data.length; i++) {
			if(data[i][1] == field_name) {
				continue;
			}
			fields.push(data[i][1]);
		}
		var html = $("#tplOperand").html();
		html = $(html);
		html.find(".field_sel").html("");
		for(var i = 0; i < fields.length; i++) {
			var opt = $("<option/>");
			opt.attr("value", fields[i]);
			opt.html(fields[i]);
			html.find(".field_sel").append(opt);
		}
		//Leo W , vamos a cargar las BD/Tablas y campos para que queden lista para usarse
		html.find("select[name='field_database[]']").empty()
		for (let index = 0; index < window.databases.length; index++) {
			const db = window.databases[index];
			var option = $('<option></option>').attr("value", db.id).text(db.name);
			html.find("select[name='field_database[]']").append(option);
		}
		//loadTables(html,'field');
		//loadFields(html,'field');

		$("#tplOperand").html(html[0].outerHTML);

		html = $("#tplOperandGroup").html();
		html = $(html);
		html.find(".field_sel").html("");
		for(var i = 0; i < fields.length; i++) {
			var opt = $("<option/>");
			opt.attr("value", fields[i]);
			opt.html(fields[i]);
			html.find(".field_sel").append(opt);
		}
		//Leo W , vamos a cargar las BD/Tablas y campos para que queden lista para usarse
		html.find("select[name='group_database[]']").empty()
		for (let index = 0; index < window.databases.length; index++) {
			const db = window.databases[index];
			var option = $('<option></option>').attr("value", db.id).text(db.name);
			html.find("select[name='group_database[]']").append(option);
		}
		//loadTables(html,'group');
		//loadFields(html,'group');
		
		$("#tplOperandGroup").html(html[0].outerHTML);
		
		//html.find("select[name='field_database[]']").trigger( "change" );
		//html.find("select[name='group_database[]']").trigger( "change" );
		//html.find("select[name='operand_type[]']").trigger( "change" );
		//html.find("select[name='operandgroup_type[]']").trigger( "change" );
	}

	var loadTableData = function(database) {
		$("[name='table']").html("");
		var selected_table = $("input[name='selectedtable']").val();
		
		for(var i = 0 ; i < database.tables.length; i++) {
        	var opt = $("<option />");
			opt.attr("value",database.tables[i].table_name);
			opt.html(database.tables[i].table_name);
			$("[name='table']").append(opt);
        }
		
		if(selected_table.length > 0) {
			if($("[name='table']").find("option[value='" + selected_table + "']").length > 0) {
				$("[name='table']").val(selected_table);
			}
		}

		$("input[name='selectedtable']").val($("[name='table']").val());		
		loadFieldData(database);
	}

	var loadFieldData = function(database) {
		var table;
		var table_name = $("input[name='selectedtable']").val();
		for(var i = 0; i < database.tables.length; i++) {
			if(database.tables[i].table_name == table_name){
				table = database.tables[i];
				break;
			}
		} 

		if(table) {

			var tplCont = $("#tplOperand")[0].innerHTML;
			tplCont = $(tplCont);
    		var sels = tplCont.find(".table_field_sel");

			var cntopt = $("<select/>");
			for(var i = 0; i < table.fields.length; i++) {
				var opt = $("<option />");
				opt.attr("value", table.fields[i].name);
				opt.html(table.fields[i].name);
				cntopt.append(opt);
			}
    		
    		for(var i = 0; i < sels.length; i++) {
    			var sel = $(sels[i]);
    			sel.html(cntopt.html());    			
    		}

    		$("#tplOperand").html(tplCont[0].outerHTML);


    		tplCont = $("#tplOperandGroup")[0].innerHTML;
			tplCont = $(tplCont);
    		sels = tplCont.find(".table_field_sel");

			var cntopt = $("<select/>");
			for(var i = 0; i < table.fields.length; i++) {
				var opt = $("<option />");
				opt.attr("value", table.fields[i].name);
				opt.html(table.fields[i].name);
				cntopt.append(opt);
			}
    		
    		for(var i = 0; i < sels.length; i++) {
    			var sel = $(sels[i]);
    			sel.html(cntopt.html());    			
    		}

    		$("#tplOperandGroup").html(tplCont[0].outerHTML);
		}
		
	}

	var editField = function(event) {
		event.preventDefault();
		var dt = $("#dtFields").DataTable();
		var row = dt.row($(this).closest("tr"));
		var idx = row.index();
		var dataRow = row.data();
		var dataRaw = dataRow[0];
		var data = htmlJson(dataRaw);
		
		data["idx"] = idx;
		$("#formField")[0].reset();
		$("#formField input[type='hidden']").val("");
		$("#cntOperands").html("");
		loadSelectFields(data["fieldname"]);
		if("operand_id" in data) {
			for(var i = 0; i < data["operand_id"].length; i++) {
				var value_field = (data["value_field"])?data["value_field"][i]:"";
				var value_table = (data["value_table"])?data["value_table"][i]:"";
				var value_const = (data["value_const"])?data["value_const"][i]:"";
				var dO = {
					operand_id: data["operand_id"][i],
					operand_type: data["operand_type"][i],
					value_field: value_field,
					value_table: value_table,
					value_const: value_const,
					//Leo w
					field_database: (data["field_database"])?data["field_database"][i]:"",
					field_table: (data["field_table"])?data["field_table"][i]:""
				};
				addOperand(dO, "cntOperands");
			}
		}
		jsonForm(data, $("#formField"));
		var selch = $("#formField .sel-name-ch");
		for(var i = 0; i < selch.length; i++) {
			selCPName($(selch[i]));
		}
		$("#modalFields").modal("show");
	}

	var editFieldGroup = function(event) {
		event.preventDefault();
		var dt = $("#dtFieldsGroup").DataTable();
		var row = dt.row($(this).closest("tr"));
		var idx = row.index();
		var dataRow = row.data();
		var dataRaw = dataRow[0];
		var data = htmlJson(dataRaw);
		data["idx"] = idx;
		$("#formFieldGroup")[0].reset();
		$("#formFieldGroup input[type='hidden']").val("");
		$("#cntOperandsGroup").html("");
		loadSelectFields();
		if("operandgroup_id" in data) {
			for(var i = 0; i < data["operandgroup_id"].length; i++) {
				var valuegroup_field = (data["valuegroup_field"])?data["valuegroup_field"][i]:"";
				var valuegroup_table = (data["valuegroup_table"])?data["valuegroup_table"][i]:"";
				var valuegroup_const = (data["valuegroup_const"])?data["valuegroup_const"][i]:"";
				var dO = {
					operandgroup_id: data["operandgroup_id"][i],
					operandgroup_type: data["operandgroup_type"][i],
					valuegroup_field: valuegroup_field,
					valuegroup_table: valuegroup_table,
					valuegroup_const: valuegroup_const,
					//Leo w
					group_database: (data["group_database"])?data["group_database"][i]:"",
					group_table: (data["group_table"])?data["group_table"][i]:""
				};
				addOperandGroup(dO, "cntOperandsGroup");
			}
		}
		jsonForm(data, $("#formFieldGroup"));
		var selch = $("#formFieldGroup .sel-name-ch");
		for(var i = 0; i < selch.length; i++) {
			selCPName($(selch[i]));
		}		
		$("#modalFieldsGroup").modal("show");
	}

	var removeField = function(event) {
		event.preventDefault();
		var dt = $("#dtFields").DataTable();
		var row = dt.row($(this).closest("tr"));
		row.remove().draw(false);
	}
	
	var removeFieldGroup = function(event) {
		event.preventDefault();
		var dt = $("#dtFieldsGroup").DataTable();
		var row = dt.row($(this).closest("tr"));
		row.remove().draw(false);
	}

	var addField = function(event) {
		if(event) {
			event.preventDefault();
		}
		$("#cntOperands").html("");
		$("#formField")[0].reset();
		$("#formField input[type='hidden']").val("");
		var selch = $("#formField .sel-name-ch");
		for(var i = 0; i < selch.length; i++) {
			selCPName($(selch[i]));
		}
		loadSelectFields();
		$("#modalFields").modal("show");
	}

	var addFieldGroup = function(event) {
		if(event) {
			event.preventDefault();
		}
		$("#cntOperandsGroup").html("");
		$("#formFieldGroup")[0].reset();
		$("#formFieldGroup input[type='hidden']").val("");
		var selch = $("#formFieldGroup .sel-name-ch");
		for(var i = 0; i < selch.length; i++) {
			selCPName($(selch[i]));
		}
		loadSelectFields();
		$("#modalFieldsGroup").modal("show");
	}

	var addOperandEvent = function(event, ovalues) {
		if(event) {
			event.preventDefault();
		}
		var cnt = "cntOperands";
		addOperand(ovalues, cnt);
	}

	var addOperandGroupEvent = function(event, ovalues) {
		if(event) {
			event.preventDefault();
		}
		var cnt = "cntOperandsGroup";
		addOperandGroup(ovalues, cnt);
	}

	var addOperand = function(ovalues, cnt) {
		
		var html = $("#tplOperand").html();
		
		html = $(html);
		$("#" + cnt).append(html);
		if(ovalues) {
			if("operand_id" in ovalues) {
				html.find("[name^='operand_id']").val(ovalues["operand_id"]);
			}
			if("operand_type" in ovalues) {
				html.find("[name^='operand_type']").val(ovalues["operand_type"]);
				if(ovalues["operand_type"] == 2)
				{
					html.find("select[name='field_database[]']").removeAttr('disabled');
					html.find("select[name='field_table[]']").removeAttr('disabled');
				}
			}
			if("value_field" in ovalues) {
				html.find("[name^='value_field']").val(ovalues["value_field"]);
			}
			/*if("value_table" in ovalues) {
				html.find("[name^='value_table']").val(ovalues["value_table"]);
			}*/
			if("value_const" in ovalues) {
				html.find("[name^='value_const']").val(ovalues["value_const"]);
			}

			if("field_database" in ovalues) {
				html.find("[name^='field_database']").val(ovalues["field_database"]);
				html.find("select[name='field_database[]']").trigger( "change" );
			}
			if("field_table" in ovalues) {
				html.find("[name^='field_table']").val(ovalues["field_table"]);
				html.find("select[name='field_table[]']").trigger( "change" );
			}
			if("value_table" in ovalues) {
				html.find("[name^='value_table']").val(ovalues["value_table"]);
			}
		}
		html.find(".del-operand").click(delOperand);
		var opType = html.find(".sel-operand-type");
		chOperandType(opType);
		opType.change(eventOperantType);
	}

	var addOperandGroup = function(ovalues, cnt) {
		var html = $("#tplOperandGroup").html();
		html = $(html);
		$("#" + cnt).append(html);
		if(ovalues) {
			if("operandgroup_id" in ovalues) {
				html.find("[name^='operandgroup_id']").val(ovalues["operandgroup_id"]);
			}
			if("operandgroup_type" in ovalues) {
				html.find("[name^='operandgroup_type']").val(ovalues["operandgroup_type"]);
				if(ovalues["operandgroup_type"] == 2)
				{
					html.find("select[name='group_database[]']").removeAttr('disabled');
					html.find("select[name='group_table[]']").removeAttr('disabled');
				}
			}
			if("valuegroup_field" in ovalues) {
				html.find("[name^='valuegroup_field']").val(ovalues["valuegroup_field"]);
			}
			/*if("valuegroup_table" in ovalues) {
				html.find("[name^='valuegroup_table']").val(ovalues["valuegroup_table"]);
			}*/
			if("valuegroup_const" in ovalues) {
				html.find("[name^='valuegroup_const']").val(ovalues["valuegroup_const"]);
			}

			if("group_database" in ovalues) {
				html.find("[name^='group_database']").val(ovalues["group_database"]);
				html.find("select[name='group_database[]']").trigger( "change" );
			}
			if("group_table" in ovalues) {
				html.find("[name^='group_table']").val(ovalues["group_table"]);
				html.find("select[name='group_table[]']").trigger( "change" );
			}
			if("valuegroup_table" in ovalues) {
				html.find("[name^='valuegroup_table']").val(ovalues["valuegroup_table"]);
			}
		}
		html.find(".del-operand").click(delOperand);
		var opType = html.find(".sel-operand-type");
		chOperandType(opType);
		opType.change(eventOperantType);
	}

	var delOperand = function(event) {
		if(event) {
			event.preventDefault();
		}
		$(this).closest(".cnt-operand").remove();
	}

	var changeFormatField = function(event){
		var val = $("#fieldFormatNumber").val();
		var parent = $("#fieldFormatNumber").closest(".cnt-modal");
		var fields = parent.find(".cnt-num-format").hide();
		for(var i = 0; i < fields.length; i++) {
			$(fields[i]).find("input").val(0);	
		}		
		parent.find(".cnt-num-format-" + val).show();
	}

	var changeFormatFieldGroup = function(event){
		var val = $("#fieldFormatGroupNumber").val();
		var parent = $("#fieldFormatGroupNumber").closest(".cnt-modal");
		var fields = parent.find(".cnt-num-format").hide();
		for(var i = 0; i < fields.length; i++) {
			$(fields[i]).find("input").val(0);	
		}		
		parent.find(".cnt-num-format-" + val).show();
	}

	var changeSelName = function(event) {
		selCPName($(this));
	}

	var selCPName = function(sel) {
		var val = sel.val();
		var fname = sel.attr("name");
		var fhname = $("input[name='" + fname + "name']");
		var opt = sel.find("option[value='" + val + "']");
		if(fhname.length > 0) {
			fhname.val(opt.html());
		}
	}

	var eventOperantType = function(event) {
		chOperandType($(this));
	}		

	var chOperandType = function(sel) {
		var parent = sel.closest(".cnt-operand");
		var val = sel.val();
		parent.find("[data-type]").hide();
		parent.find("[data-type='" + val + "']").show();
	}

	var formJSON = function(form) {
		var form_data = new FormData(form);
		var data = {};
		form_data.forEach((value, key) => {
			if(key.indexOf("[") >= 0) {
				key = key.replace("[", "");
				key = key.replace("]", "");
				if(!(key in data)) {
					data[key] = [];
				}
				data[key].push(value);
			}
			else
			{				
				data[key] = value
			}				
		});
		return data;
	}

	var jsonFormHTML = function(data) {
		var html = "";
		for(var key in data) {
			var value = data[key];
			if(Array.isArray(value)) {
				var id = "";
				if("id" in data) {
					id = data["id"];
				}
				for(var j = 0; j < data[key].length; j++) {
					var elm = '<input type="hidden" name="' + key + '[' + id + '][]" value="' + data[key][j] + '" />';
					html += elm;
				}
			}
			else{
				var elm = '<input type="hidden" name="' + key + '[]" value="' + value + '" />';
				html += elm;
			}
		}
		return html;
	}

	var htmlJson = function(html) {
		html = $("<div>" + html + "</div>");
		var ipts = html.find("input");
		var data = {};
		var expr = /[\[]([a-zA-Z0-9])*[\]]/;
		for(var i = 0; i < ipts.length; i++) {
			var ipt = $(ipts[i]);
			var name = ipt.attr("name");
			name = name.replace(expr, '');
			if(expr.test(name)) {
				var aname = name.replace(expr, '');
				if(!(aname in data)) {
					data[aname] = [];
				}
				data[aname].push($(ipt).val());
			}
			else {
				data[name] = $(ipt).val();
			}				
		}
		return data;
	}

	var jsonForm = function(data, form) {
		for(var key in data) {
			form.find("[name='" + key + "']").val(data[key]);
		}		
	}

	var addRowDT = function(dt, data, indexes) {
		var dataA = [];
		for(var idx in indexes) {
			var key = indexes[idx];
			if(key in data) {
				dataA[idx] = data[key];	
			}
			else {
				dataA[idx] = "";
			}
		}
		dt.row.add(dataA).draw(false);
	}	

	var updateRowDT = function(dt, data, indexes) {
		var idxR = data["idx"];
		if($.isNumeric(idxR)) {
			var dataA = [];
			for(var idx in indexes) {
				var key = indexes[idx];
				if(key in data) {
					dataA[idx] = data[key];	
				}
				else {
					dataA[idx] = "";
				}
			}
			
			idxR = parseInt(idxR);
			dt.row(idxR).data(dataA).draw(false);
		}
	}

	var saveFieldData = function(event) {
		if(event) {
			event.preventDefault();
		}
		var form = $("#formField")[0];
		var data = formJSON(form);
		data["new"] = false;
		if("fieldid" in data) {
			if(data["fieldid"].length == 0) {
				var index = $("#dtFields").data("index");
				index = parseInt(index) + 1;
				$("#dtFields").data("index", index);
				data["fieldid"] = "F" + index.toString();
				data["id"] = "F" + index.toString();
				data["new"] = true;
			}
		}
		var dataHTML = jsonFormHTML(data);
		data["html"] = dataHTML;
		data["edit"] = 1;
		data["remove"] = 1;
		var indexField = {
			0: "html",
			1: "fieldname",
			2: "fieldoperationtypename",
			3: "edit",
			4: "remove"
		}
		var dt = $("#dtFields").DataTable();
		if(data["new"]) {
			addRowDT(dt, data, indexField);
		}
		else {
			updateRowDT(dt, data, indexField);
		}
		$("#modalFields").modal("hide");
	}

	var saveFieldDataGroup = function(event) {
		if(event) {
			event.preventDefault();
		}
		var form = $("#formFieldGroup")[0];
		var data = formJSON(form);
		data["new"] = false;
		if("fieldgroupid" in data) {
			if(data["fieldgroupid"].length == 0) {
				var index = $("#dtFieldsGroup").data("index");
				index = parseInt(index) + 1;
				$("#dtFieldsGroup").data("index", index);
				data["fieldgroupid"] = "G" + index.toString();
				data["groupid"] = "G" + index.toString();
				data["new"] = true;
			}
		}
		data["id"] = data["fieldgroupid"];
		var dataHTML = jsonFormHTML(data);
		data["html"] = dataHTML;
		data["edit"] = 1;
		data["remove"] = 1;
		var indexField = {
			0: "html",
			1: "groupname",
			2: "groupdisplayname",
			3: "fieldgrouptypename",
			4: "operationgrouptypename",
			5: "edit",
			6: "remove"
		}
		var dt = $("#dtFieldsGroup").DataTable();
		if(data["new"]) {
			addRowDT(dt, data, indexField);
		}
		else {
			updateRowDT(dt, data, indexField);			
		}
		$("#modalFieldsGroup").modal("hide");
	}

	var saveDataFields = function(event) {
		event.preventDefault();
		$(".errorHelper").hide();
		/*var database = $("[name='database']").val(); Leo w ya no se va a usar
		var table = $("[name='database']").val();
		let df = false, tf = false;
		if(!database) {
			df = true;
		}
		if(database) {
			if(database.length == 0) {
				df = true;
			}
		}
		if(df){ Leo w ya no se va a usar
			$("#database_error").show();
			$("#database_error strong").html("Debe seleccionar una base de datos");
		}

		if(!table) {
			tf = true;
		}
		if(table) {
			if(table.length == 0) {
				tf = true;
			}
		}
		if(tf){
			$("#table_error").show();
			$("#table_error strong").html("Debe seleccionar una tabla de datos");
		}
		if(tf) {
			return false;
		}*/
		$("#dtFields").DataTable().destroy();
		$("#dtFieldsGroup").DataTable().destroy();		
		var form = $("#form-fields")[0];
		var formData = new FormData(form);
		var url = $("#form-fields").attr("action");
		$.ajax({
		     url: url,
		     data: formData,
		     cache: false,
		     contentType: false,
		     processData: false,
		     dataType: "json",
		     type: 'POST',
	         success: function (data) {
		         if(data.error) {
		        	initFieldsDt();
		        	initFieldsGroupDt();
			     }
	           	 location.href = url_return;
	         },
	         error: function (data) {
	        	 initFieldsDt();
	        	 initFieldsGroupDt();
	         }	            
	    });
	}	
</script>