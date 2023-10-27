<!-- Modal -->
<div id="edit_groups" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header text-center">
        	<button type="button" class="close" data-dismiss="modal">&times;</button>
        	<h3>Grupos de Menús</h3>        
		</div>
        <div class="modal-body">        
        		<div class="row">            		
            		<div class="col-md-6">
            			<select class="form-control group-selector" id="groupSelector">
            			
            			</select>
            		</div>
            		<div class="col-md-6">
            			<button class="btn btn-primary" type="button" id="btnEditGroup"><span class="fa fa-edit"></span> Editar Grupo</button>
            			<button class="btn btn-success" type="button" id="btnNewGroup"><span class="fa fa-plus"></span> Agregar Grupo</button>            			
            		</div>            		
            	</div>
            	<hr/>
            	<div class="alert alert-danger alert-groups">
        			<h5>Se ha eliminado correctamente el registro</h5>
        		</div>
        		<div class="alert alert-success alert-groups">
        			<h5>Se ha modificado correctamente el registro</h5>
        		</div>
            	<form id="formGroups" action="{{route('groups.store')}}">
            		{{csrf_field()}}
            		<input type="hidden" name="group_id" value="" />
                	<div class="row group-data" style="margin-top:15px;">
                		<div class="col-md-12">
                			<label class="col-md-4 control-label">Nombre: </label>
                			<div class="col-md-8">
                				<input type="text" class="form-control" name="name" id="groupNameModal" required="required" />
                			</div>
                		</div>
                	</div>
                	<div class="row group-data" style="margin-top:15px;">
                		<div class="col-md-12" style="margin-bottom:10px;">
                			<h4>Menús</h4>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu1">
                                	<input type="checkbox" name="groupMenu[]" value="1" id="menu1"> Energía y Potencia
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu2">
                                	<input type="checkbox" name="groupMenu[]" value="2" id="menu2"> Contadores
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu3">
                                	<input type="checkbox" name="groupMenu[]" value="3" id="menu3"> Consumo de Energía
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu4">
                                	<input type="checkbox" name="groupMenu[]" value="4" id="menu4"> Análisis de Potencia
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu5">
                                	<input type="checkbox" name="groupMenu[]" value="5" id="menu5"> Simulación de Potencia
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu6">
                                	<input type="checkbox" name="groupMenu[]" value="6" id="menu6"> Mercado Energético
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu7">
                                	<input type="checkbox" name="groupMenu[]" value="7" id="menu7"> Seguimiento de Objeticos
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu8">
                                	<input type="checkbox" name="groupMenu[]" value="8" id="menu8"> Comparador de Ofertas
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu9">
                                	<input type="checkbox" name="groupMenu[]" value="9" id="menu9"> Simulación de Facturas
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu10">
                                	<input type="checkbox" name="groupMenu[]" value="10" id="menu10"> Informes y Alertas
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu11">
                                	<input type="checkbox" name="groupMenu[]" value="11" id="menu11"> Emisiones de CO2
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu12">
                                	<input type="checkbox" name="groupMenu[]" value="12" id="menu12"> Exportar Datos
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu13">
                                	<input type="checkbox" name="groupMenu[]" value="13" id="menu13"> Área Cliente
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu14">
                                	<input type="checkbox" name="groupMenu[]" value="14" id="menu14"> Analizadores Submetering
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu15">
                                	<input type="checkbox" name="groupMenu[]" value="15" id="menu15"> Producción Submetering
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu16">
                                	<input type="checkbox" name="groupMenu[]" value="16" id="menu16"> Indicadores Energéticos
                                </label>
                            </div>
                		</div>
                		<div class="col-md-6">
                			<div class="checkbox">
                            	<label for="menu17">
                                	<input type="checkbox" name="groupMenu[]" value="17" id="menu17"> Manual Submetering
                                </label>
                            </div>
                		</div>
						<div class="col-md-6">
							<div class="checkbox">
								<label for="menu18">
									<input type="checkbox" name="groupMenu[]" value="18" id="menu18"> Representación Datos
								</label>
							</div>
						</div>
                		<div class="col-md-12 text-center" style="text-align: center; margin-top:12px;">
                            <button class="btn btn-success" type="submit"><span class="fa fa-save"></span> Guardar Grupo</button>
                            <button class="btn btn-danger" type="button" id="btnDeleteGroup"><span class="fa fa-trash"></span> Eliminar Grupo</button>
                		</div>
                	</div>
            	
        		</form>
        </div>
        
    </div>

  </div>
</div>
