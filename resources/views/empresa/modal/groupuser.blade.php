<div class="modal fade" tabindex="-1" role="dialog" id="modalGroupUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Asignar Contadores</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<div class="row" id="contentGroupUser"></div>
            </div>
            <div class="modal-footer">
            	<div>
                	<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-times"></span> Cancelar</button>
                	<button type="button" class="btn btn-success" id="btnSaveGroupUser"><span class="fa fa-save"></span> Guardar</button>
                </div>
            </div>
    	</div>
  </div>
</div>
<template id="tplGroupUser">
	<div>
    	<div class="col-12">
    		<div class="form-group form-row">
    			<label class="col-12 col-md-6 text-center"></label>
    			<select class="form-control col-12 col-md-6"></select>
    		</div>
    	</div>
	</div>
</template>
<script type="text/javascript">
<!--
	var initializeGroupUser = function() {
		var groups = $("#modalGroupUser").data("groups");
		var energy = $("#modalGroupUser").data("energy");		
		
		$("#contentGroupUser").html("");
		var url = "{{route('groups.get')}}";

	    $.ajax({
     		type: "GET",
            url: url,
            success: function (data) {
            	if(data.error) {
    				return false;
            	}
            	var tpl = getTplGroupUser(data.groups);            	
            	for(var i = 0; i < energy.length; i++) {
					var html = $(tpl);
					html.find("select").data("energy", energy[i][0]);
					html.find("label").html(energy[i][1]);
					$("#contentGroupUser").append(html);
					if(groups[parseInt(energy[i][0])]) {
						html.find("select option[value='" + groups[energy[i][0]] + "']").prop("selected", "selected");						
					}
					$("#modalGroupUser").modal("show");
            	}	
            },
            error: function (data) {
                console.log(data);
            }	            
        });
	}

	var getTplGroupUser = function(groups) {
		var tpl = $("#tplGroupUser").html();
		tplObj = $(tpl);
		for(var i = 0; i < groups.length; i++) {
			var option =$("<option/>");
			option.attr("value", groups[i].id);
			option.html(groups[i].nombre);
			tplObj.find("select").append(option);
		}
		return tplObj.html();
	}

	var saveDataGroupUser = function(event) {
		var sel = $("#modalGroupUser select");
		var energy = [];
		var groups = [];
		for(var i = 0; i < sel.length; i++) {
			var e = $(sel[i]).data("energy");
			energy.push(e);
			groups.push($(sel[i]).val());
		}
		$("#modalGroupUser").data("energy", energy);
		$("#modalGroupUser").data("groups", groups);
		var saveData = $("#modalGroupUser").data("save");
		saveData();
	}

	$(document).ready(function(){
		$("#modalGroupUser").data("init", initializeGroupUser);

		$("#btnSaveGroupUser").click(saveDataGroupUser);
	});
//-->
</script>
