<div class="modal fade" tabindex="-1" role="dialog" id="modalUserEnergy">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Asignar Dispositivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<h5>Contadores</h5>
            	<hr/>
            	<div class="row" id="contentUserEnergy"></div>
            	<h5 class="mt-5">Analizadores</h5>
            	<hr/>
            	<div class="row" id="contentUserAnalyzers"></div>
            </div>
            <div class="modal-footer">
            	<div>
                	<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-success" id="btnSaveUserEnergy"><span class="fa fa-save"></span> Guardar</button>
                </div>
            </div>
    	</div>
  </div>
</div>
<template id="tplEnergyCheck">
	<div class="col-md-6">
		<div class="checkbox">
        	<label for="menu1">
            	<input type="checkbox" value="1"> <span></span>
            </label>
        </div>
	</div>
</template>
<template id="tplAnalyzerCheck">
	<div class="col-md-6">
		<div class="checkbox">
        	<label for="menu1">
            	<input type="checkbox" value="1"> <span></span>
            </label>
        </div>
	</div>
</template>
<script type="text/javascript">
<!--
	var initializeUserEnergy = function() {
		var dataEnergy = $("#modalUserEnergy").data("energy");
		var dataUser = $("#modalUserEnergy").data("userEnergy");
		
		$("#contentUserEnergy").html("");
		for(var i = 0; i < dataEnergy.length; i++) {
			var html = $("#tplEnergyCheck").html();
			html = $(html);
			html.find("input").attr("value", dataEnergy[i][0]);
			html.find("span").html(dataEnergy[i][1]);
			$("#contentUserEnergy").append(html);
		}

		for(var i = 0; i < dataUser.length; i++) {
			var chk = $("#contentUserEnergy input[value='" + dataUser[i] + "']").prop("checked", "checked");
		}

		var dataAnalyzers = $("#modalUserEnergy").data("analyzers");
		var dataUser = $("#modalUserEnergy").data("userAnalyzers");
		
		$("#contentUserAnalyzers").html("");
		for(var i = 0; i < dataAnalyzers.length; i++) {
			var html = $("#tplAnalyzerCheck").html();
			html = $(html);
			html.find("input").attr("value", dataAnalyzers[i][0]);
			html.find("span").html(dataAnalyzers[i][1]);
			$("#contentUserAnalyzers").append(html);
		}

		for(var i = 0; i < dataUser.length; i++) {
			var chk = $("#contentUserAnalyzers input[value='" + dataUser[i] + "']").prop("checked", "checked");
		}
	}

	var saveDataUserEnergy = function(event) {
		var checked = $("#contentUserEnergy input:checked");
		var userEnergy = [];
		for(var i = 0; i < checked.length; i++) {
			userEnergy.push($(checked[i]).val());
		}
		$("#modalUserEnergy").data("userEnergy", userEnergy);

		var checked = $("#contentUserAnalyzers input:checked");
		var userAnalyzer = [];
		for(var i = 0; i < checked.length; i++) {
			userAnalyzer.push($(checked[i]).val());
		}
		$("#modalUserEnergy").data("userAnalyzer", userAnalyzer);
		
		var saveData = $("#modalUserEnergy").data("save");
		saveData();
	}

	$(document).ready(function(){
		$("#modalUserEnergy").data("init", initializeUserEnergy);

		$("#btnSaveUserEnergy").click(saveDataUserEnergy);
	});
//-->
</script>
