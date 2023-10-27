<div class="modal fade" tabindex="-1" role="dialog" id="modalGroupsAnalyzers">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Grupos de Analizadores</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<div class="row" id="cntAssignGroupAnalyzer">
            		<div class="col-12">
            			<div class="row">
            				<div class="col-5">
            					<div class="form-group">
                    				<select class="form-control" id="groupAnalyzerSelect">
                    					<option value="1">Grupo 1</option>	
                    				</select>
                    			</div>
            				</div>
            				<div class="col-7 mb-2">
            					<div class="btn-group" role="group" aria-label="Basic example">
                					<button class="btn btn-info btn-sm" id="assignGroupAnalyzer"><span class="fa fa-check"></span> Asignar</button>
                					<button class="btn btn-success btn-sm" id="btnAddGroupAnalyzer"><span class="fa fa-plus"></span> Agregar</button>
                					<button class="btn btn-primary btn-sm" id="btnModifyGroupAnalyzer"><span class="fa fa-edit"></span> Editar</button>
                					<button class="btn btn-danger btn-sm" id="btnDeleteGroupAnalyzer"><span class="fa fa-times"></span> Eliminar</button>
            					</div>
            				</div>
            			</div>
            			
            		</div>
            		<div class="col-12">
            			<div class="row"  id="cntAssignedAnalyzerGroups"></div>
            		</div>
            	</div>
            	<form action="" method="post" id="formGroupAnalyzer" enctype="multipart/form-data">
            		{!! csrf_field() !!}
                	<div class="row" id="cntFormGroupAnalyzer">
                		<div class="col-12 mb-2">
                			<div class="form-group">
                				<label>Nombre</label>
                				<input class="form-control" type="text" name="name" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                		<div class="col-12 col-lg-6 offset-lg-3">
            				<div class="card">
            					<div class="card-header bg-submeter-4 text-white text-center">
            						<h5>Imagen Analizadores</h5>
            					</div>
            					<div class="card-body text-center pt-1">
            						<div class="row" id="cntimage" style="display:none;">
            							<div class="col-4 offset-4 px-0 py-2">
            								<img src="" alt="Analizadores" class="img-fluid img-thumbnail">
            							</div>
            						</div>
            						<div class="row">
            							<div class="col-12 p-0">
                    						<div class="form-group mb-1">
    										 	<input type="file" name="image_analyzer" id="image_analyzer" class="file-hidden" >
    										 	<label class="btn btn-primary" for="image_analyzer"><span class="fa fa-upload"></span> Examinar</label>                                                                                                                                                        
                                            </div>
                                        </div>
                                    </div>
            					</div>
            				</div>
            			</div>
                		<div class="col-12 mt-4">
                			<div class="form-group text-right">
                				<button class="btn btn-success" id="btnAddAnalyzerGroup"><span class="fa fa-plus"></span> Agregar Analizador</button>
                			</div>
                			<div class="row mx-0" id="analizerSelectGroup"></div>
						</div>
                		<div class="col-12 mt-4">
                			<div class="form-group text-right">
                				<button class="btn btn-success" id="btnAddDependencyGroup">
									<span class="fa fa-plus"></span> Agregar Dependencia
								</button>
                			</div>
                			<div class="row mx-0" id="dependencySelectGroup"></div>
                		</div>
						<div class="col-12 mt-4 border-top text-right" style="padding-top:3rem;padding-bottom:2rem;">
							<input type="checkbox" name="rest_operation" id="rest_operation" /> <label for="rest_operation">Activar operación RESTO</label>
						</div>
                	</div>
            	</form>
            	<div class="row" id="cntDeleteGroupAnalyzer">
            		<div class="col-12">
            			<h4 class="text-center">¿Está seguro que desea eliminar este grupo?</h4>
            			<h5 class="text-center group-analyzer-name text-danger mt-4"></h5>
            			<form id="formDeleteGroupAnalyzer" method="post" action="">
            				{!! csrf_field() !!}
            				<input type="hidden" name="id" value="" />
            				<input type="hidden" name="_method" value="DELETE" />
            			</form>
            		</div>
            	</div>
            </div>
            <div class="modal-footer">
            	<div id="cntBtnGroupAnalyzerAssign">
                	<button type="button" class="btn btn-secondary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                	<button type="button" class="btn btn-primary" id="btnSaveAssignGroup"><span class="fa fa-save"></span> Guardar</button>
                </div>
                <div id="cntBtnGroupAnalyzerSave">
                	<button type="button" class="btn btn-danger btn-group-analyzer-assign"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-primary" id="btnSaveGroupAnalyzer"><span class="fa fa-save"></span> Guardar</button>                	
                </div>
                <div id="cntBtnGroupAnalyzerDelete">
                	<button type="button" class="btn btn-primary btn-group-analyzer-assign"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-danger" id="btnConfirmDeleteGroupAnalyzer"><span class="fa fa-trash"></span> Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<template id="analyzerEditFields">
	<input type="hidden" name="_method" value="PATCH" />
	<input type="hidden" name="id" value="" />
</template>
<template id="analyzerAssignButton">
	<button class="btn btn-success btn-sm btn-assign-analyzer" data-id="###"><span class="fa fa-check"></span></button>
</template>
<template id="analyzerEditButton">
	<button class="btn btn-primary btn-sm btn-edit-analyzer" data-id="###"><span class="fa fa-pen-fancy"></span></button>
</template>
<template id="analyzerDeleteButton">
	<button class="btn btn-danger btn-sm btn-delete-analyzer" data-id="###"><span class="fa fa-times"></span></button>
</template>
<template id="analyzerGroupButton">
	<div class="analyzer-grup-b col-2">
    	<button class="btn btn-sm btn-primary mb-2 btn-delete-assign">
    		_XXX_ <span class="fa fa-times"></span>		
    	</button>
    	<input type="hidden" name="groupAnalyzerA[]" value="" />
    </div>
</template>

<template id="tplGroupSelectAnalyzer">
	<div class="col-6 mx-0 analyzer-control-container">
		<div class="form-group">
			<div class="row mx-0">
				<div class="col-9 mx-0">
					<select class="form-control" name="analyzers[]"></select>
				</div>
				<div class="col-3 mx-0">
					<button class="btn btn-danger btn-delete-analyzer-control"><span class="fa fa-times"></span></button>
				</div>
			</div>
		</div>
	</div>
</template>

<template id="tplGroupSelectDependency">
	<div class="col-6 mx-0 dependency-control-container">
		<div class="form-group">
			<div class="row mx-0">
				<div class="col-9 mx-0">
					<select class="form-control" name="dependencies[]"></select>
				</div>
				<div class="col-3 mx-0">
					<button class="btn btn-danger btn-delete-dependency-control"><span class="fa fa-times"></span></button>
				</div>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">
<!--
var urlAddGroupAnalyzer = '{{route("analyzergroup.save")}}';
var urlEditGroupAnalyzer = '{{route("analyzergroup.update", ["analyzer_group_id" => "_XXX_"])}}';
var urlDeleteGroupAnalyzer = '{{route("analyzergroup.delete", ["analyzer_group_id" => "_XXX_"])}}';
var urlShowGroupAnalyzer = '{{route("analyzergroup.show", ["analyzer_group_id" => "_XXX_"])}}';
var urlSelectGroupAnalyzer = '{{route("analyzergroup.list")}}';
var urlSelectAnalyzers = '{{route("analyzer.list")}}';

var getSelectGroupAnalyzer = function(refreshAssigned) {
	$.ajax({
 		type: "GET",
 		url: urlSelectGroupAnalyzer,
	    dataType: "json",
        success: function (data) {
            if(data.error) {
				return false;
            }       	
        	$("#groupAnalyzerSelect").html("");
        	for(var i = 0; i < data.options.length; i++) {
				var option = $("<option/>");
				option.val(data.options[i].value);
				option.html(data.options[i].name);
				$("#groupAnalyzerSelect").append(option);
        	}
        	$("#cntAssignedAnalyzerGroups").html("");
        	showAssignedAnalyzerGroups();
        },
        error: function (data) {
            console.log(data);
        }	            
    });
}

var getSelectAnalyzers = function() {
	$.ajax({
 		type: "GET",
 		url: urlSelectAnalyzers,
	    dataType: "json",
        success: function (data) {
            if(data.error) {
				return false;
            }
        	var html = $("#tplGroupSelectAnalyzer").html();
        	html = $(html);
        	var sel = html.find("select");
        	sel.html("");

        	var html2 = $("#tplGroupSelectDependency").html();
        	html2 = $(html2);
        	var sel2 = html2.find("select");
        	sel2.html("");
        	for(var i = 0; i < data.options.length; i++) {
				var option = $("<option></option>");
				option.val(data.options[i].value);
				option.html(data.options[i].name);
				sel.append(option);
				var option2 = $("<option></option>");
				option2.val(data.options[i].value);
				option2.html(data.options[i].name);
				sel2.append(option2);
        	}
        	$("#tplGroupSelectAnalyzer").html(html[0].outerHTML);
			$("#tplGroupSelectDependency").html(html2[0].outerHTML);
        },
        error: function (data) {
            console.log(data);
        }	            
    });
};

var assignGroupAnalyzer = function(event) {
	if(event != undefined) {
		event.preventDefault();
	}
	var id = $("#groupAnalyzerSelect").val();
	var name =  $("#groupAnalyzerSelect option[value='" + id + "']").html();
	var html = $("#analyzerGroupButton").html();
	html = html.replace("_XXX_", name);
	html = $(html);
	html.find("input[name='groupAnalyzerA[]']").val(id);
	$("#cntAssignedAnalyzerGroups").append(html);
	html.find(".btn-delete-assign").click(deleteAssignAnalyzerGroup);
}

var newAnalyzerGroup = function() {
	cleanFormGroupAnalyzer();
	$("#formGroupAnalyzer")[0].reset();
	$("#formGroupAnalyzer").find("input[name='_method']").remove();
	$("#formGroupAnalyzer").find("input[name='id']").remove();
	$("#modalGroupsAnalyzers").data("action", urlAddGroupAnalyzer);
	
}

var openEditGroupAnalyzers = function(event) {
	event.preventDefault();
	cleanFormGroupAnalyzer();
	$("#formGroupAnalyzer")[0].reset();
	var id = $("#groupAnalyzerSelect").val();
	var method = $("<input/>");
	method.prop("name", "_method");
	method.val("PATCH");
	method.prop("type", "hidden");
	var ipt = $("<input/>");
	ipt.prop("name", "id");
	ipt.val(id);
	ipt.prop("type", "hidden");
	$("#formGroupAnalyzer").append(ipt);
	$("#formGroupAnalyzer").append(method);
	editAnalyzerGroup(id);
}

var editAnalyzerGroup = function(id) {
	var action = urlShowGroupAnalyzer.replace("_XXX_", id);
	$.ajax({
 		type: "GET",
 		url: action,
	    dataType: "json",
        success: function (data) {
            if(data.error) {
				return false;
            }
            $("#formGroupAnalyzer")[0].reset();
            for(var key in data.data){
				var valData = data.data[key];
				$("#formGroupAnalyzer [type='hidden'][name='" + key + "']").val(valData);
				$("#formGroupAnalyzer [type='text'][name='" + key + "']").val(valData);
				$("#formGroupAnalyzer [type='number'][name='" + key + "']").val(valData);
				$("#formGroupAnalyzer select[name='" + key + "']").val(valData);
				$("#formGroupAnalyzer [type='radio'][name='" + key + "'][value='" + valData + "']").prop("checked", "checked");
				if($("#formGroupAnalyzer [type='checkbox'][name='" + key + "']").length && valData===1)
					$("#formGroupAnalyzer [type='checkbox'][name='" + key + "']").prop("checked", "checked");
            }
            $("#formGroupAnalyzer #cntimage").hide();
            if(data.data["file_image"].length > 0) {
            	$("#formGroupAnalyzer #cntimage").show();
            	$("#formGroupAnalyzer img").attr("src", data.data["file_image"]);
            }
            if(data.analyzers) {
				for(var i = 0; i < data.analyzers.length; i++) {
					var html = addAnalyzerSelectControl(undefined);
					html.find("select").val(data.analyzers[i]);
				}
            }
			if(data.data.dependencies_ids && data.data.dependencies_ids.length>0){
				for(var i = 0; i < data.data.dependencies_ids.length; i++) {
					var html = addDependencySelectControl(undefined);
					html.find("select").val(data.data.dependencies_ids[i]);
				}
			}
            var url = urlEditGroupAnalyzer.replace("_XXX_", id);
        	$("#modalGroupsAnalyzers").data("action", url);       	
        	
        },
        error: function (data) {
            console.log(data);
        }	            
    });
	
}

var deleteAnalyzerSelectControl = function(event) {
	event.preventDefault();
	var parent = $(this).closest(".analyzer-control-container");
	if(parent.length > 0) {
		parent.remove();
	}
};

var deleteDependencySelectControl = function(event){
	event.preventDefault();
	var parent = $(this).closest(".dependency-control-container");
	if(parent.length > 0) parent.remove();
};

var deleteAssignAnalyzerGroup = function(event) {
	event.preventDefault();
	var parent = $(this).closest(".analyzer-grup-b");
	if(parent.length > 0) {
		parent.remove();
	}
}

var deleteAnalyzerGroup = function(event) {
	event.preventDefault();
	$("#cntFormGroupAnalyzer").hide();
	$("#cntBtnGroupAnalyzerSave").hide();
	$("#cntDeleteGroupAnalyzer").show();
	$("#cntBtnGroupAnalyzerDelete").show();
	$("#cntBtnGroupAnalyzerAssign").hide();
	$("#cntAssignGroupAnalyzer").hide();
	$("#analizerSelectGroup").html("");
	$("#dependencySelectGroup").html("");
	var gValue = $("#groupAnalyzerSelect").val();
	var analyzerGroupName = $("#groupAnalyzerSelect option[value='" + gValue + "']").html();
	$("#formDeleteGroupAnalyzer input[name='id']").val(gValue);
	$(".group-analyzer-name").html(analyzerGroupName);
}

var confirmDeleteGroupAnalyzer = function(event) {
	event.preventDefault();
	var form = $("#formDeleteGroupAnalyzer")[0];
	var formData = new FormData(form);	
	var id = $("#formDeleteGroupAnalyzer [name='id']").val();
	var action = urlDeleteGroupAnalyzer.replace("_XXX_", id);
	$.ajax({
 		type: "POST",
 		url: action,
 		data: formData,
 		cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
        success: function (data) {
        	if(data.error) {
            	return false;
        	}
        	resetGroupAnalyzer();
        },
        error: function (data) {
            console.log(data);
        }	            
    });
}

var addAnalyzerSelectControl = function(event) {
	if(event) {
		event.preventDefault();
	}
	var html = $("#tplGroupSelectAnalyzer").html();
	html = $(html);
	$("#analizerSelectGroup").append(html);
	html.find(".btn-delete-analyzer-control").click(deleteAnalyzerSelectControl);
	return html;
};

var addDependencySelectControl = function(event){
	if(event) event.preventDefault();
	var html = $("#tplGroupSelectDependency").html();
	html = $(html);
	$("#dependencySelectGroup").append(html);
	html.find(".btn-delete-dependency-control").click(deleteDependencySelectControl);
	return html;
};

var sendFormGroupAnalyzer = function(event) {
	var form = $("#formGroupAnalyzer")[0];
	var action = $("#modalGroupsAnalyzers").data("action");
	var formData = new FormData(form);
	$("#formGroupAnalyzer input, #formGroupAnalyzer select").removeClass("border-error");
	$("#formGroupAnalyzer .invalid-feedback").html("");
	$.ajax({
 		type: "POST",
 		url: action,
 		data: formData,
 		cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
        success: function (data) {
        	if(data.error) {
            	console.log(data);
            	for(key in data.messages){
                	var input = $("#energyForm [name='" + key + "']");
                	if(input.length > 0) {
                    	input.addClass("border-error");
                		var parent = input.closest(".form-group");
                		if(parent.length > 0) {
                			parent.find(".invalid-feedback").html(data.messages[key]).show();
                		}
                	}
            	}
				return false;
        	}
        	resetGroupAnalyzer();
        	//returnFunction(data.data);
        },
        error: function (data) {
            console.log(data);
        }	            
    });
}

var cleanFormGroupAnalyzer = function() {
	$("#formGroupAnalyzer")[0].reset();
	$("#cntFormGroupAnalyzer").show();
	$("#cntDeleteGroupAnalyzer").hide();
	$("#cntBtnGroupAnalyzerSave").show();
	$("#cntBtnGroupAnalyzerDelete").hide();
	$("#cntBtnGroupAnalyzerAssign").hide();
	$("#cntAssignGroupAnalyzer").hide();
	$("#analizerSelectGroup").html("");
	$("#dependencySelectGroup").html("");
}

var resetGroupAnalyzer = function() {
	getSelectAnalyzers();
	getSelectGroupAnalyzer();
	$("#formGroupAnalyzer #cntimage").hide();
	$("#cntDeleteGroupAnalyzer").hide();
	$("#cntFormGroupAnalyzer").hide();
	$("#cntBtnGroupAnalyzerSave").hide();
	$("#cntBtnGroupAnalyzerDelete").hide();
	$("#cntBtnGroupAnalyzerAssign").show();
	$("#cntAssignGroupAnalyzer").show();
	$("#modalGroupsAnalyzers").modal('show');	
}

var showAssignedAnalyzerGroups = function(){
	var dataAssigned = $("#modalGroupsAnalyzers").data("assigned");
	for(var i = 0; i < dataAssigned.length; i++) {
		$("#groupAnalyzerSelect").val(dataAssigned[i]);
		assignGroupAnalyzer(undefined);
	}
}


var saveAssignGroup = function(event) {
	event.preventDefault();
	var ipt = $("#cntAssignedAnalyzerGroups input");
	var data = [];
	for(var i = 0; i < ipt.length; i++) {
		var ip = $(ipt[i]);
		data.push(ip.val());
	}
	$("#modalGroupsAnalyzers").data("aGroups", data);
	var fSave = $("#modalGroupsAnalyzers").data("saveDataF");
	if(fSave) {
		fSave(data);
	}
}

$(document).ready(function(){
	$("#modalGroupsAnalyzers").data("reset", resetGroupAnalyzer);
	$("#assignGroupAnalyzer").click(assignGroupAnalyzer);
	$("#btnAddAnalyzerGroup").click(addAnalyzerSelectControl);
	$("#btnAddGroupAnalyzer").click(newAnalyzerGroup);
	$("#btnModifyGroupAnalyzer").click(openEditGroupAnalyzers);
	$("#btnConfirmDeleteGroupAnalyzer").click(confirmDeleteGroupAnalyzer);
	$("#btnDeleteGroupAnalyzer").click(deleteAnalyzerGroup);
	$("#btnSaveGroupAnalyzer").click(sendFormGroupAnalyzer);
	$("#btnSaveAssignGroup").click(saveAssignGroup);
	$("#btnAddDependencyGroup").click(addDependencySelectControl);
	$(".btn-group-analyzer-assign").click(function(event){
		resetGroupAnalyzer();
	});
});
//-->
</script>