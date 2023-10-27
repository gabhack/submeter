<div class="drop-men" >
    <ul class=" nav_1">
  		<li class="dropdown">
            @if(isset($ctrl) && $ctrl == 1)
                <a href="#" class="dropdown-toggle dropdown-at" data-toggle="dropdown">
                  @if(isset($dir_image_count))
                    <img width="60" height="60" src="{{asset($dir_image_count)}}">
                  @elseif(Auth::user()->_perfil)
                    @if(!is_null(Auth::user()->_perfil->avatar))
                      <img width="60" height="60" src="{{asset(Auth::user()->_perfil->avatar)}}">
                    @else
                      <img width="60" height="60" src="{{asset('images/avatar.png')}}">
                    @endif
                  @else                
                    <img width="60" height="60" src="{{asset('images/avatar.png')}}">
                  @endif
                  <span class=" name-caret">{!! \Auth::user()->name !!}<i class="caret"></i></span>
                </a>
            @else
              <a href="#" class="dropdown-toggle dropdown-at" data-toggle="dropdown">
              	@if(isset($dir_image_count))
                    <img width="60" height="60" src="{{asset($dir_image_count)}}">
                @elseif(isset((Auth::user()->_perfil)))
                  @if(!is_null(Auth::user()->_perfil->avatar))
                    <img width="60" height="60" src="{{asset(Auth::user()->_perfil->avatar)}}">
                  @else
                  <img width="60" height="60" src="{{asset('images/avatar.png')}}">
                  @endif
                @else
                  <img width="60" height="60" src="{{asset('images/avatar.png')}}">
                @endif
                <span class=" name-caret">{!! $user->name !!}
                  <i class="caret"></i>
                </span>
              </a>            
            @endif
            <ul class="dropdown-menu " role="menu">
              <li><a href="{{ route('perfil.form') }}"><i class="fa fa-user"></i>Editar Perfil</a></li>		                
              <li><a href="{{ route('logout') }}" 
                  onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i>Salir</a>
                   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                   </form>
              </li>
            </ul>
      </li>
      <li class="dropdown" style="margin-right:20px;margin-left:5px;">
      	<button type="button" class="btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      		<i class="fa fa-power-off"></i>
      	</button>
      </li>	           
    </ul>
</div>