<!-- Modal -->
<div class="modal fade" id="newModalConnection" tabindex="-1" role="dialog" aria-labelledby="newModalConnectionAria" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newModalConnectionAria">Nueva conexión</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

  <form method="POST" action="{{ route('production.ConnectionSave') }}">   
    {{ csrf_field() }}        

        <div class="modal-body">
              <div class="form-group">
                    <div class="row">
                            <div class="col-6">
                                <label for="count_label">Nombre de etiqueta <span>*</span></label>
                                <input name="count_label" required="required"  type="text" class="form-control" id="count_label">
                            </div>

                             <div class="col-6">
                                <label for="host">Host <span >*</span></label>
                                <input name="host" required="required" type="text" class="form-control" id="host">
                            </div>
                    </div>
               </div>


               <div class="form-group">
                    <div class="row">
                            <div class="col-6">
                                <label for="port">Puerto <span>*</span></label>
                                <input name="port" required="required"  type="text" class="form-control" id="port">
                            </div>

                             <div class="col-6">
                                <label for="database">Base de datos <span >*</span></label>
                                <input name="database" required="required" type="text" class="form-control" id="database">
                            </div>
                    </div>
               </div>

                <div class="form-group">
                    <div class="row">
                            <div class="col-6">
                                <label for="username">Nombre de Usuario <span>*</span></label>
                                <input name="username" required="required"  type="text" class="form-control" id="username">
                            </div>

                             <div class="col-6">
                                <label for="password">Contraseña <span >*</span></label>
                                <input name="password" required="required" type="text" class="form-control" id="password">
                            </div>
                    </div>
               </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar conexión</button>
        </div>
 </form> 

    </div>
  </div>
</div>