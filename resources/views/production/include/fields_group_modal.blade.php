<div class="modal fade cnt-modal" tabindex="-1" role="dialog" id="modalFieldsGroup">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Campo Agrupación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<form action="" method="post" id="formFieldGroup" enctype="multipart/form-data">
            		<input type="hidden" name="groupid" value="" />
            		<input type="hidden" name="idx" value="" />
            		<input type="hidden" name="fieldgroupid" value="" />
            		<input type="hidden" name="fieldgrouptypename" value="" />
            		<input type="hidden" name="operationgrouptypename" value="" />
                	<div class="row">
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Nombre Campo</label>
                				<input class="form-control" type="text" name="groupname" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Nombre a Mostrar</label>
                				<input class="form-control" type="text" name="groupdisplayname" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>                	
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Tipo de Campo</label>
                				<select class="form-control sel-name-ch" name="fieldgrouptype">
                					<option value="1">Agua</option>
                					<option value="2">Electrico</option>
                					<option value="3">Gas</option>
                      				    <option value="4">Vapor</option>
                         				<option value="5">Aire</option>
				                        <option value="6">Caudal</option>
				                        <option value="7">Neumatica</option>
				                        <option value="8">Compresores</option>
				                        <option value="9">Temperatura</option>
				                        <option value="10">Produccion</option>
				                        <option value="11">Luminosidad</option>
				                        <option value="12">Humedad</option>
				                        <option value="13">Emisiones</option>
				                        <option value="14">Gases</option>
				                        <option value="15">Presion</option>
				                        <option value="16">Velocidad</option>
				                        <option value="17">Humedad relativa</option>
				                        <option value="18">Otros</option>
          				                <option value="19">Potencia</option>      				
          				                <option value="20">Potencia Nominal</option>   
          				                <option value="21">Potencia Máxima</option>   
          				                <option value="22">Potencia Mínima</option>   
          				                <option value="23">Potencia Activa</option>   
          				                <option value="24">Potencia Reactiva</option>   
          				                <option value="25">Potencia React. Inductiva</option>   
          				                <option value="26">Potencia React. Capacitiva</option>   
          				                <option value="27">Potencia Óptima</option>   
          				                <option value="28">Potencia Disponible</option>   
          				                <option value="29">T. Distorsión Tensión</option>   
          				                <option value="30">T. Distorsión Intensidad</option>   
          				                <option value="31">FDP</option>   
          				                <option value="32">kW</option>   
          				                <option value="33">kWh</option>   
          				                <option value="34">Pérdidas</option>   
          				                <option value="35">Consumo</option>   
          				                <option value="36">Nivel de Carga</option>   
          				                <option value="37">Carga</option>   
          				                <option value="38">Carga Óptima</option>   
          				                <option value="39">Rendimiento</option>   
          				                <option value="40">Rendimiento Óptimo</option>   
          				                <option value="41">Producción</option>   
          				                <option value="42">Volumen</option>   
          				                <option value="43">Fallos</option>   
          				                <option value="44">Nivel Calidad</option>   
          				                <option value="45">%</option>   
          				                <option value="46">Ratio</option>   
          				                <option value="47">IDEn</option>   
          				                <option value="48">Indicador</option>   
          				                <option value="49">Valor de referencia</option>   
          				                <option value="50">PCI</option>   
          				                <option value="51">PCS</option>   										
          				                <option value="52">Disponibilidad</option>  
								</select>
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Mostrar en</label>
                				<select class="form-control" name="fieldgroupshow">
                					<option value="1">CSV</option>
                					<option value="2">Gráfica</option>
                					<option value="4">Total</option>
                					<option value="3">CSV y Gráfica</option>
                					<option value="5">Total y CSV</option>
                					<option value="6">Gráfica y Total</option>
                					<option value="7">Todos los Rubros</option>                					
                				</select>
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Tipo de Operación</label>
                				<select class="form-control sel-name-ch" name="operationgrouptype">
                					<option value="1" data-min="1" data-max="1">SUMATOTAL</option>
                					<option value="2" data-min="1" data-max="1">PROMEDIO</option>
                					<option value="3" data-min="1" data-max="1">MEDIANA</option>
                					<option value="4" data-min="1" data-max="1"> MIN</option>
                					<option value="5" data-min="1" data-max="1">MAX</option>
                					<option value="6" data-min="1" data-max="1"> DESVIACIÓN ESTANDAR</option>              					
                				</select>
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>                	                		
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Tipo de Número</label>
                				<select class="form-control" id="fieldFormatGroupNumber" name="numbergrouptype">
                					<option value="1">DECIMAL</option>
                					<option value="2">ENTERO</option>
                				</select>
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>                	
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Unidades</label>
                				<input class="form-control" type="text" name="unitsgroup" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>  	
                		<div class="col-6 mb-2  cnt-num-format cnt-num-format-1">
                			<div class="form-group">
                				<label>Número de Decimales</label>
                				<input class="form-control" type="number" min="1" step="1" name="decimalsgroup" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Color</label>
                				<input class="form-control col-2" type="color" name="fieldgroupcolor" value="" style="min-height:35px;" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                	</div>
                	<div class="row">
                		<div class="col-9 mb-2">
                			<h5>Operandos</h5>
                		</div>
                		<div class="col-3 mb-2">
                			<button type="button" class="btn btn-success" id="btnAddOperandFieldGroup"><span class="fa fa-plus"></span> Agregar</button>
                		</div>
                	</div>
                	<div class="row" id="cntOperandsGroup">
                	</div>
				</form>
				{{-- @Leo W , se modifica el template para operadores, de forma que cada operador pueda tener su Base de datos,Tabla y Campo --}}
            	<template id="tplOperandGroup">
            		<div class="col-12 cnt-operand">
            			<input type="hidden" name="operandgroup_id[]" value="">
                		<div class="row bx-operand">
							<div class="col-10">
								<div class="row">
									<div class="col-6">
										<label for="database[]">Base de datos</label>
										<select class="form-control" name="group_database[]" disabled ></select>
									</div>
									<div class="col-6">
										<label for="table[]">Tabla</label>
										<select class="form-control" name="group_table[]" disabled></select>
									</div>
									<div class="col-6">
										<div class="form-group">
											<label>Tipo de Operando</label>
											<select class="form-control sel-operand-type" name="operandgroup_type[]">
												<option value="1">CAMPO</option>
												<option value="2">CAMPO BASE DE DATOS</option>
												<option value="3">CONSTANTE</option>              					
											</select>
											<div class="invalid-feedback"></div>
										</div>                			
									</div>
									<div class="col-6">
										<div class="form-group">
											<label>Valor</label>
											<select class="form-control field_sel" name="valuegroup_field[]" data-type="1"></select>
											<select class="form-control table_field_sel" name="valuegroup_table[]" data-type="2"></select>
											<input class="form-control" type="number" name="valuegroup_const[]" value=""  data-type="3" />
											<div class="invalid-feedback"></div>
										</div>                			
									</div>
								</div>
							</div>
							<div class="col-2 flex-center">
                    			<button class="btn btn-danger del-operand"><span class="fa fa-trash"></span></button>
                    		</div>
                    		
                    		
                    	</div>
                	</div>
            	</template>
            	
            </div>
            <div class="modal-footer">
            	<div>
                	<button type="button" class="btn btn-secondary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
                	<button type="button" class="btn btn-primary" id="btnSaveFieldGroup"><span class="fa fa-save"></span> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>