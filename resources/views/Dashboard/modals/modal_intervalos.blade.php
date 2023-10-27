<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h2 class="modal-title">Intervalo Temporal</h2>
			</div>
			<form id="form_intervalos" name="form_intervalos" action="{{route('config.interval')}}" method="POST">
				{{ csrf_field() }}
				<div class="modal-body">
					<div class="form-row align-items-center">
						<div class="col-auto">
							<p>Por favor, indique el intervalo para el período que desea observar</p><br>
						</div>
						<div class="col-auto text-center">
							<label class="mr-sm-2" for="option_interval">Intervalo </label>
							<input type="hidden" name="user_id" value="{{$user->id}}">
							<select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="option_interval" name="option_interval" id="option_interval" onchange="changeFunc();">
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
								@if( Route::currentRouteName() !== 'exportar.datos' )
									<option value="9">Personalizado</option>
									<!-- <option value="9">Personalizado</option> -->
								@endif
							</select>
						</div>
						@if( Route::currentRouteName() !== 'exportar.datos' )
							<div class="col-auto text-center" id="div_datatimes">
								<div class="col-md-12" id="cont_div">
									<br>
									<div class="col-md-6">
										<label>Desde: </label>
										<input type ="text" id="datepicker" id="date_from_personalice" name="date_from_personalice">
									</div>
									<div class="col-md-6">
										<label>Hasta: </label>
										<input type ="text" id="datepicker2" id="date_to_personalice" name="date_to_personalice">
									</div>
									<br>
								</div>
							</div>
						@endif
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