<div class="content counters">
  @foreach($user->energy_meters as $i => $contador)
    @if(App\Http\Controllers\GroupsController::checkContadorMenu($user->id, $menuId, $contador->id))         
      @php
        $contadores[$contador->id] = $contador->count_label;
        if($contador->id == $user->current_count->meter_id){
          $active = $contador->id;
        }           
      @endphp          
    @endif
  @endforeach
  @if (isset($contadores))
    <ul class="btn-list">
      @foreach ($contadores as $id => $label)<li>
        @php
        $params = [$user->id, $id];
        if(isset($origin_link)){
          $params["return_to"] = $origin_link;
        }
        @endphp
        <a class="btn @if(isset($active) && $id === $active) active @endif" href="{{route('energymeter.change', $params)}}">
          <i class="fa fa-clock-o"></i><span>{{$label}}</span>
        </a>
      </li>@endforeach
    </ul>
    <div class="dropdown">
      <button type="button" class="btn active dropdown__button">
        <i class="fa fa-clock-o"></i>
        @if (isset($active))
          <span>{{$contadores[$active]}}</span>
        @endif
      </button>
      <ul class="dropdown__menu">
        @foreach ($contadores as $id => $label)
          @if(isset($active) && ($id != $active))
            @php
            $params = [$user->id, $id];
            if(isset($origin_link)){
              $params["return_to"] = $origin_link;
            }
            @endphp
            <li class="dropdown__item">
              <a href="{{route('energymeter.change', $params)}}">
                <i class="fa fa-clock-o"></i>
                <span>{{$label}}</span>
              </a>
            </li>
          @endif
        @endforeach
      </ul>
    </div>
  @endif
</div>
