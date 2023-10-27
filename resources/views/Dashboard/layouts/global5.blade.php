<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">   
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Submeter 4.0 | Home</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/Submeter_Favicon.ico')}}">
  
    <link rel="stylesheet" type="text/css" href="{{asset('css/normalize.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/styles5.css')}}">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/928e3d06ab.js" crossorigin="anonymous"></script>    

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-2.2.2.min.js"></script>

    @yield('otherlinks')
       
    {{-- Scripts for pdf --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    {{-- Scripts for pdf --}}

    @if(!isset($chartjsnew))
      <script src="{{asset('js/Chart.js')}}"></script>
    @else
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" ></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-piechart-outlabels" ></script>
      <script src="https://www.gstatic.com/charts/loader.js"></script>
      <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    @endif
  </head>
		
  <body>
    <header class="header">
      <div class="header__logo">
        <a href="{!! route('home') !!}">
          <img
            src="{{asset('images/submeter_logo_white_big.png')}}"
            alt="Submeter 4.0"
          />
        </a>
      </div>

      <button id="btn-nav-menu" class="header__button" type="button">
        <span class="header__hamburguer"></span>
      </button>

      <h1 class="header__title">
        @if(isset($titulo))
          @if($titulo == 'Emisiones CO2')
            Emisiones CO<sub>2</sub>
          @else
            {{$titulo}}
          @endif
        @endif
      </h1>  

      @php
        if (isset($dir_image_count)) {
          $imgAlt = $user->name;
          $imgSrc = asset($dir_image_count);
        }
        elseif (isset((Auth::user()->_perfil))) {
          if(!is_null(Auth::user()->_perfil->avatar)){
            $imgAlt = $user->name;
            $imgSrc = asset(Auth::user()->_perfil->avatar);
          }
          else {
            $imgAlt = "avatar";
            $imgSrc = asset('images/avatar.png');
          }
        }
        else {
          $imgAlt = "avatar";
          $imgSrc = asset('images/avatar.png');
        }
      @endphp
      <div class="header__user-icon">
        <img alt="{!! $imgAlt !!}" src="{{$imgSrc}}">
      </div>

      <div class="header__menu">
        {{-- <button class="header__menu-button" type="button"><i class="fas fa-cog"></i></button> --}}
        <div class="dropdown">
          <button class="header__menu-button dropdown__button" type="button"><i class="fas fa-cog"></i></button>
          <ul class="dropdown__menu">
            <li class="dropdown__item"><a href="{{ route('perfil.form') }}"><i class="fa fa-user"></i>Editar perfil</a></li>
            <li class="dropdown__item"><a data-submeter-toggle="modal" href="#logout-modal"><i class="fas fa-sign-out-alt"></i>Salir</a></li>
          </ul>
        </div>

        @if(isset($ctrl) && $ctrl == 1)
          <a href="{{ route('enterprise.index') }}" class="header__menu-button">
            <i class="fas fa-undo"></i>
          </a>
        @else
          <button class="header__menu-button" data-submeter-toggle="modal" data-target="#logout-modal">
            <i class="fas fa-power-off"></i>
          </button>
        @endif
      </div>
    </header>
  
    {{-- MENU LATERAL --}}
    @include('Dashboard.includes.menu5',array('user_log' => $user))  
  
    <main class="main">
      @yield('intervals')
      @yield('counters')
      <div class="content main-content">
        @yield('content')
      </div>
    </main>

    <footer class="footer">
      <p> &copy; @php echo date("Y"); @endphp Submeter 4.0. Todos los derechos reservados</p>
    </footer>
    <form id="logout-form" class="hidden" action="{{ route('logout') }}" method="POST">
      {{ csrf_field() }}
    </form>

    {{-- MODALES --}}
    @yield('modals')
    @include('Dashboard.modals.modal_logout')
    
    {{-- SCRIPTS --}}
    @yield('scripts') 

    <script>
      //Side nav
      const body = document.querySelector("body")
      const btnMenu = document.querySelector("#btn-nav-menu")
      const navMenu = document.querySelector("#nav-menu")
      const navLinks = document.querySelector("#nav-links")
      let navIsVisible = false

      btnMenu.addEventListener("click", toggleNav)
      navMenu.addEventListener("click", (e) => {
        if (e.target === navMenu) {
          toggleNav()
        }
      })

      function toggleNav() {
        if (!navIsVisible) {
          navMenu.classList.add("open")
          navLinks.classList.add("open")
          navIsVisible = true
        } else {
          navMenu.classList.remove("open")
          navLinks.classList.remove("open")
          navIsVisible = false
        }
      }

      //Current nav link
      const currentUrl = document.URL.split('?')[0]
      const urlRegExp = new RegExp(currentUrl)
      const navItems = navLinks.querySelectorAll("li")
      
      navItems.forEach(navItem => {
        const anchor = navItem.querySelector("a")
        if (isNull(anchor)){
          return
        }
        const href = anchor.getAttribute("href")
        if (isNull(href)){
          return
        }
        if (href.match(urlRegExp)){
          navItem.classList.add("active")
        }
      })
 

      //Collapse
      const collapseBtnList = document.querySelectorAll('[data-submeter-toggle="collapse"]')
      collapseBtnList.forEach((btn) => {
        const collapse = getTarget(btn)

        if (isNull(collapse)){
          return
        }

        btn.addEventListener("click", toggleCollapse)

        function toggleCollapse(){
          collapse.classList.toggle("d-none")
        }
      })

      //Modal
      const modalBtnList = Array.from(
        document.querySelectorAll('[data-submeter-toggle="modal"]')
      )

      modalBtnList.forEach((btn) => {
        const modal = getTarget(btn)

        if (isNull(modal)){
          return
        }

        const modalCloseBtnList = Array.from(
          modal.querySelectorAll("[data-close-modal]")
        )

        btn.addEventListener("click", openModal)

        modalCloseBtnList.forEach((modalCloseBtn) => {
          modalCloseBtn.addEventListener("click", closeModal)
        })

        if (modal.hasAttribute("data-close-modal")) {
          modal.addEventListener("click", (e) => {
            if (e.target === modal) {
              closeModal()
            }
          })
        }

        function openModal() {
          modal.classList.add("open")
        }

        function closeModal(){
          modal.classList.remove("open")
        }
      })

      function getTarget(element){
        let targetId

        if (element.tagName === "BUTTON"){
          targetId = element.dataset.target
        } else if (element.tagName === "A"){
          targetId = element.getAttribute("href")
        } 

        if (!isNull(targetId)){
          const target = document.querySelector(targetId)
          return target
        }

        return
      }

      function isNull(variable){
        return variable === undefined || variable === null
      }
    </script>
	<script src="{{asset('js/app.js')}}"></script>
	<script>
		CanvasJS.addCultureInfo("es", {
			decimalSeparator: ",",
			digitGroupSeparator: "."
		})
	</script>
  </body>
</html>
