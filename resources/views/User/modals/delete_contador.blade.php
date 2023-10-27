<!-- Modal -->
<div id="delete_contador_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h2>Eliminación de contador</h2>        
      </div>
      <div class="modal-body text-center">        
        <label>¿Está seguro que desea eliminar el contador <label id="contador_name"></label> del sistema?</label>
      </div>
      <div class="modal-footer text-center" style="text-align: center">
          <span class="token-container">
          {{csrf_field()}}
          </span>
          <input type="hidden" id="contador_id" name="contador_id">
          <button type="submit" id="btn-eliminar-contador" class="btn btn-form">Eliminar</button>
          <button type="button" class="btn btn-form" data-dismiss="modal">Cancelar</button>
      </div>
    </div>

  </div>
</div>
