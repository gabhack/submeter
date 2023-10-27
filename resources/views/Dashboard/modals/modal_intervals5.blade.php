<div id="interval-modal" class="modal-wrapper" data-close-modal>
  <div class="modal">
    <div class="modal__header">
      <h2 class="modal__title">Intervalo Temporal</h2>
      <button class="modal__close" data-close-modal><i class="fas fa-times"></i></button>
    </div>

    <div class="modal__body">
      <p class="modal__message">Por favor, indique el intervalo para el per&iacute;odo que desea observar.</p>
      <form id="form_intervalos" name="form_intervalos" action="{{route('config.interval')}}" method="POST">
        <input type="hidden" name="user_id" value="{{$user->id}}">
        {{ csrf_field() }}
        <label for="option_interval">Intervalo</label>
        <select id="option_interval" name="option_interval" class="interval-select">
          <option value="2">Hoy</option>
          <option value="1">Ayer</option>
          <option value="3">Semana Actual</option>
          <option value="4">Semana Anterior</option>
          <option value="5">Mes Actual</option>
          <option value="6">Mes Anterior</option>
          <option value="10">Trimestre Actual</option>
          <option value="7">&Uacute;ltimo Trimestre</option>
          <option value="11">Año Actual</option>
          <option value="8">&Uacute;ltimo Año</option>
          @if( Route::currentRouteName() !== 'exportar.datos' )
            <option value="9">Personalizado</option>
          @endif
        </select>

        @if( Route::currentRouteName() !== 'exportar.datos' )
          <div id="div_datatimes">
            <br>
            <label>Desde: </label>
            <div>
              <input type="text" id="datepicker" name="date_from_personalice">
            </div><br>
            <label>Hasta: </label>
            <div>
              <input type="text" id="datepicker2" name="date_to_personalice">
            </div>
          </div>
        @endif
      </form>
    </div>

    <div class="modal__footer">
      <div class="btn-container">
        <button type="button" class="btn" data-close-modal>Cancelar</button>
        <button type="submit" form="form_intervalos" class="btn btn-primary">Establecer</button>
      </div>
    </div>
  </div>
</div>