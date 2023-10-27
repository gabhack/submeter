<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Intervalo Temporal</h3>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<form id="form_intervalos" name="form_intervalos" action="{{route('config.interval')}}" method="POST">
				<input type="hidden" name="user_id" value="{{$user->id}}">
				{{ csrf_field() }}
				<div class="modal-body">
					<div class="container-fluid">
						<div class="col-12">
							<p>Por favor, indique el intervalo para el período que desea observar</p>
						</div>
						<div class="col-12 text-center mt-1">
							<div class="form-group row">
                                <label for="staticEmail" class="col-sm-2 offset-sm-2 col-form-label">
                                	Intervalo
                                </label>
                                <div class="col-sm-6">
                                  <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="option_interval" name="option_interval">
        					        <option value="2">Hoy</option>
        					        <option value="1">Ayer</option>
        					        <option value="3">Semana Actual</option>
        					        <option value="4">Semana Anterior</option>
        					        <option value="5">Mes Actual</option>
        					        <option value="6">Mes Anterior</option>
        					        <option value="10">Trimestre Actual</option>
        					        <option value="7">Último Trimestre</option>
        					        <option value="11">Año Actual</option>					        
        					        <option value="8">Último Año</option>
        					        <option value="9">Personalizado</option>
        					      </select>
                                </div>
                              </div>
						</div>
						<div class="col-12 text-center" id="div_datatimes">
							<div class="form-group row">
								<label class="col-sm-2">Desde: </label>
								<div class="col-md-4">
									<input type ="text" class="form-control" id="datepicker" name="date_from_personalice">
								</div>
								<label class="col-sm-2 mt-2 mt-sm-0">Hasta: </label>
								<div class="col-md-4">
									<input type ="text" class="form-control" id="datepicker2" name="date_to_personalice">
								</div>
							</div>
						</div>
					</div>					  
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="sumbit" class="btn btn-primary">Establecer</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
<!-- 
	$(document).ready(function(){
		$("#option_interval").change(changeIntervalOption);
		changeIntervalOption();

		$( "#datepicker" ).datepicker({
	    	dateFormat:'yy-mm-dd',
	    	changeMonth: true,
      		changeYear: true,
	    });

		$( "#datepicker2" ).datepicker({
	    	dateFormat:'yy-mm-dd',
	    	changeMonth: true,
      		changeYear: true,
	    });
	});

	function changeIntervalOption(){
		var val = $("#option_interval").val();
		if(val == "9"){
			$("#div_datatimes").show();
		}
		else{
			$("#div_datatimes").hide();
		}
	}
-->
</script>