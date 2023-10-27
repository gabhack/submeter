<div class="modal fade cnt-modal" tabindex="-1" role="dialog" id="modalFields">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title">Campo Personalizable</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<form action="" method="post" id="formField" enctype="multipart/form-data">
            		<input type="hidden" name="id" value="" />
            		<input type="hidden" name="idx" value="" />
            		<input type="hidden" name="fieldid" value="" />
            		<input type="hidden" name="fieldoperationtypename" value="" />
                	<div class="row">
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Nombre Campo</label>
                				<input class="form-control" type="text" name="fieldname" value="" />
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>                		
                		<div class="col-6 mb-2">
                			<div class="form-group">
                				<label>Tipo de Operación</label>
                				<select class="form-control sel-name-ch" name="fieldoperationtype">
                					<option value="1" data-min="1" data-max="1">NUMERO</option>
                					<option value="2" data-min="1" data-max="1000">SUMA</option>
                					<option value="3" data-min="2" data-max="2">RESTA</option>
                					<option value="4" data-min="1" data-max="1000">MULTIPLICACION</option>
                					<option value="5" data-min="2" data-max="2"> DIVISION</option>
                					<!-- <option value="6" data-min="2" data-max="2">X A LA Y</option>
                					<option value="7" data-min="2" data-max="2"> RAIZ X DE Y</option>
                					<option value="8" data-min="1" data-max="1"> RAIZ CUADRADA</option>
                					<option value="9" data-min="1" data-max="1"> CUADRADO</option>-->             					
                				</select>
                				<div class="invalid-feedback"></div>
                			</div>                			
                		</div>
                	</div>                	
                	<div class="row">
                		<div class="col-9 mb-2">
                			<h5>Operandos</h5>
                		</div>
                		<div class="col-3 mb-2">
                			<button type="button" class="btn btn-success" id="btnAddOperandField"><span class="fa fa-plus"></span> Agregar</button>
                		</div>
                	</div>
                	<div class="row" id="cntOperands">
                	</div>
				</form>
				{{-- @Leo W , se modifica el template para operadores, de forma que cada operador pueda tener su Base de datos,Tabla y Campo --}}
            	<template id="tplOperand">
            		<div class="col-12 cnt-operand">
            			<input type="hidden" name="operand_id[]" value="">
                		<div class="row bx-operand">
							<div class="col-10">
								<div class="row">
									<div class="col-6">
										<label for="database[]">Base de datos</label>
										<select class="form-control" name="field_database[]" disabled ></select>
									</div>
									<div class="col-6">
										<label for="table[]">Tabla</label>
										<select class="form-control" name="field_table[]" disabled></select>
									</div>
									<div class="col-6">
										<div class="form-group">
											<label>Tipo de Operando</label>
											<select class="form-control sel-operand-type" name="operand_type[]">
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
											<select class="form-control field_sel" name="value_field[]" data-type="1"></select>
											<div>
												<select class="form-control table_field_sel" name="value_table[]" data-type="2"></select>
											</div>
											
											<input class="form-control" type="number" name="value_const[]" value=""  data-type="3" />
											<div class="invalid-feedback"></div>
										</div>                			
									</div>
								</div>
							</div>
                    		
                    		<div class="col-2 flex-center">
                    			<button class="btn btn-outline-danger del-operand"><span class="fa fa-trash"></span></button>
                    		</div>
                    	</div>
                	</div>
            	</template>
            </div>
            <div class="modal-footer"> 
				{{-- @Leo W ,quito el div, para que se vea mejor el footer en modo responsive --}}
				<button type="button" class="btn btn-primary" id="btnSaveField"><span class="fa fa-save"></span> Guardar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><span class="fa fa-times"></span> Cerrar</button>
            </div>
        </div>
    </div>
</div>