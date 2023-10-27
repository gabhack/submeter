<ul class="nav nav-header justify-content-end mr-2 mr-sm-4">
    <li class="d-none d-sm-inline">
        @if (isset($dir_image_count))
            <img class="logo-menu img-thumbnail my-1" src="{{ asset($dir_image_count) }}">
        @elseif(isset(Auth::user()->_perfil))
            @if (!is_null(Auth::user()->_perfil->avatar))
                <img class="logo-menu img-thumbnail my-1" src="{{ asset(Auth::user()->_perfil->avatar) }}">
            @else
                <img class="logo-menu img-thumbnail my-1" src="{{ asset('images/avatar.png') }}">
            @endif
        @else
            <img class="logo-menu img-thumbnail my-1" src="{{ asset('images/avatar.png') }}">
        @endif
    </li>
    <li class="nav-item dropdown d-none d-sm-inline">
        <a class="nav-link dropdown-toggle mt-2" href="#" data-toggle="dropdown" role="button"
            aria-haspopup="true" aria-expanded="false">
            <span class="name-caret">{!! $user->name !!}
                <i class="caret"></i>
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-submeter" role="menu">
            <a class="dropdown-item pt-3 pb-1" href="{{ route('perfil.form') }}"><i class="fa fa-user mr-2"></i> Editar
                Perfil</a>
            <div class="dropdown-divider"></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
            <a class="dropdown-item pt-1 pb-2 logout-button" href="{{ route('logout') }}"><i
                    class="fa fa-sign-out-alt mr-2"></i> Salir</a>
        </div>
    </li>
    <li class="d-inline d-sm-none">
        <button class="btn btn-link my-0 dropdown-toggle no-decoration text-dark" href="#" data-toggle="dropdown"
            role="button" aria-haspopup="true" aria-expanded="false">
            @if (isset($dir_image_count))
                <img class="logo-menu img-thumbnail my-1" src="{{ asset($dir_image_count) }}">
            @elseif(isset(Auth::user()->_perfil))
                @if (!is_null(Auth::user()->_perfil->avatar))
                    <img class="logo-menu img-thumbnail my-1" src="{{ asset(Auth::user()->_perfil->avatar) }}">
                @else
                    <img class="logo-menu img-thumbnail my-1" src="{{ asset('images/avatar.png') }}">
                @endif
            @else
                <img class="logo-menu img-thumbnail my-1" src="{{ asset('images/avatar.png') }}">
            @endif
        </button>
        <div class="dropdown-menu dropdown-menu-submeter mr-4" role="menu">
            <a class="dropdown-item pt-3 pb-1" href="{{ route('perfil.form') }}"><i class="fa fa-user mr-2"></i> Editar
                Perfil</a>
            <div class="dropdown-divider"></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
            <a class="dropdown-item pt-1 pb-2 logout-button" href="{{ route('logout') }}"><i
                    class="fa fa-sign-out-alt mr-2"></i> Salir</a>
        </div>
    </li>
    <li class="d-none d-lg-inline pt-2 pl-2">
        <button type="button" class="btn logout-button" href="{{ route('logout') }}">
            <i class="fa fa-power-off"></i>
        </button>
    </li>
</ul>
<script type="text/javascript">
    <!-- 
    $(document).ready(function() {
        $(".logout-button").click(function(event) {
            event.preventDefault();
            $("#logout-form").submit();
        });
    });
    -->
</script>
