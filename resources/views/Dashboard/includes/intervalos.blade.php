<div class="content intervals">
  
  @if($label_intervalo != 'Personalizado')
    <form id="form_navegation" class="d-none" action="{{route('config.navigation')}}" method="POST">
      {{ csrf_field() }}
      <input type="hidden" name="option_interval" value="9">
      <input type="hidden" name="label_intervalo" value="{{$label_intervalo}}">
      <input type="hidden" name="date_from_personalice" value="{{$date_from}}">
      <input type="hidden" name="date_to_personalice" value="{{$date_to}}">
      <input type="hidden" name="before_navigation" id="before_navigation" value="0">
    </form>
  
    <div class="btn-list">
      <button type="submit" class="btn active" onclick="anterior()" form="form_navegation">
        <i class="fas fa-angle-double-left"></i>
      </button>
      <button type="button" class="btn active" data-submeter-toggle="modal" data-target="#interval-modal">
        <i class="fas fa-pencil-alt" style="background-color: #286090; padding: 0.2rem; border-radius: 0.3rem; margin-right: 0.3rem;"></i>
        @if(isset(Session::get('_flash')['current_date']))
          {{Session::get('_flash')['current_date']}}
        @else
          {{$label_intervalo}}
        @endif
      </button>
      <button type="submit" class="btn active" onclick="siguiente()" form="form_navegation">
        <i class="fas fa-angle-double-right"></i>
      </button>
    </div>
  @else
    <button type="button" class="btn active" data-toggle="modal" data-target="#interval-modal">
      <i class="fas fa-pencil-alt"></i>
      <span>Personalizado</span>
    </button>
  @endif
  
  <div class="interval">
    <i class="fas fa-calendar-alt"></i>
    <span>
      {{$date_from}} @if ($date_from !== $date_to) -- {{$date_to}}@endif
    </span>
  </div>
</div>
