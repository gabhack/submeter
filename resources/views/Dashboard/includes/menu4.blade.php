<ul class="nav flex-column bg-submeter-1 nav-submeter">
	@if($user_log->tipo == 1)
		<li><a href="#!"><i class="fa fa-users nav_icon"></i><span class="nav-label">Administrar usuarios</span></a></li>
		<li><a href="{{route('admin.users',[1,0])}}"><i class="fa fa-user-plus nav_icon"></i>Administradores</a></li>
		<li><a href="{{route('admin.users',[2,0])}}"><i class="fa fa-user nav_icon"></i>Clientes</a></li>
		<li><a href="{{route('enterprise.index')}}"><i class="fa fa-building nav_icon"></i>Empresas</a></li>
		<li><a href="{{route('statistics.listAll')}}"><i class="fa fa-building nav_icon"></i>IDENs & Producción</a></li>
	@endif
	@if($user_log->tipo == 2)
		@if(App\Http\Controllers\GroupsController::checkMenu(1, $user->id))
			<li>
				@if(!isset($ctrl) && $tipo_count == 1)
					<a href="{{route('resumen.energia.potencia',$user_log)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Energía y Potencia</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 2)
					<a href="{{route('resumen.energia.potencia',$user_log)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Resumen</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 3)
					<a href="{{route('resumen.energia.potencia',$user_log)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Resumen GN</span></a>
				@elseif(isset($ctrl) && $tipo_count == 1)
					<a href="{{route('resumen.energia.potencia',$user->id)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Energía y Potencia</span></a>
				@elseif(isset($ctrl) && $tipo_count == 2)
					<a href="{{route('resumen.energia.potencia',$user->id)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Resumen</span></a>
				@elseif(isset($ctrl) && $tipo_count == 3)
					<a href="{{route('resumen.energia.potencia',$user->id)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Resumen GN</span></a>
				@endif
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(2, $user->id))
			<li>
				@if(!isset($ctrl) && ($tipo_count == 1 || $tipo_count == 2 || $tipo_count == 3))
					<a href="{!! route('ver.panel.user',[$user->id, 0]) !!}"><i class="far fa-clock nav_icon "></i><span class="nav-label">Contadores</span></a>
				@elseif(isset($ctrl) && ($tipo_count == 1 || $tipo_count == 2 || $tipo_count == 3))
					<a href="{!! route('ver.panel.user',[$user->id, $ctrl]) !!}"><i class="far fa-clock nav_icon "></i><span class="nav-label">Contadores</span></a>
				@endif
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(3, $user->id))
			<li>
				@if(!isset($ctrl) && $tipo_count == 1)
					<a href="{{route('consumo.energia',$user_log)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Consumo de Energía</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 2)
					<a href="{{route('consumo.energia',$user_log)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Consumo y Generación</span></a>
				@elseif(isset($ctrl) && $tipo_count == 1)
					<a href="{{route('consumo.energia',$user->id)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Consumo de Energía</span></a>
				@elseif(isset($ctrl) && $tipo_count == 2)
					<a href="{{route('consumo.energia',$user->id)}}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Consumo y Generación</span></a>
				@endif
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(4, $user->id))
			<li>		
				@if(!isset($ctrl) && $tipo_count == 1)
					<a href="{{route('analisis.potencia2',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Análisis Potencia</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 2)
					<a href="{{route('analisis.potencia2',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Análisis Potencia</span></a>
				@elseif(isset($ctrl) && $tipo_count == 1)
					<a href="{{route('analisis.potencia2',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Análisis Potencia</span></a>
				@elseif(isset($ctrl) && $tipo_count == 2)
					<a href="{{route('analisis.potencia2',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Análisis Potencia</span></a>
				@endif
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(5, $user->id))
			<li>
				@if(!isset($ctrl) && $tipo_count == 1)
					<a href="{{route('simulacion.potencia',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Simulación Potencia</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 2)
					<a href="{{route('simulacion.potencia',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Simulación Potencia</span></a>
				@elseif(isset($ctrl) && $tipo_count == 1)
					<a href="{{route('simulacion.potencia',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Simulación Potencia</span></a>
				@elseif(isset($ctrl) && $tipo_count == 2)
					<a href="{{route('simulacion.potencia',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Simulación Potencia</span></a>
				@endif                	
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(6, $user->id))
			<li>
				@if(!isset($ctrl) && $tipo_count == 1)
					<a href="{{route('mercado.energetico',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Mercado Energético</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 2)
					<a href="{{route('mercado.energetico',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Mercado Energético</span></a>
				@elseif(isset($ctrl) && $tipo_count == 1)
					<a href="{{route('mercado.energetico',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Mercado Energético</span></a>
				@elseif(isset($ctrl) && $tipo_count == 2)
					<a href="{{route('mercado.energetico',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Mercado Energético</span></a>
				@endif                	
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(7, $user->id))
			@if($tipo_count < 3)
				<li>
					<a href="{{route('seguimiento.objetivos',$user_log)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Seguimiento Objetivos</span></a>
				</li>
			@endif
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(8, $user->id))
			<li>
			 	@if($tipo_count <= 3)
					<a href="{{route('comparador.ofertas',$user_log)}}"><i class="fa fa-euro-sign nav_icon"></i><span class="nav-label">Comparador Ofertas</span></a>
				@endif       
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(9, $user->id))
			<li>
				@if(!isset($ctrl) && $tipo_count < 3)
					<a href="{!! route('simulacion.facturas',[$user->id]) !!}"><i class="fa fa-newspaper nav_icon"></i><span class="nav-label">Simulación Facturas</span></a>
				@elseif(!isset($ctrl) && $tipo_count == 3)
					<a href="{!! route('simulacion.facturas',[$user->id]) !!}"><i class="fa fa-newspaper nav_icon"></i><span class="nav-label">Simulación Facturas</span></a>
				@elseif(isset($ctrl) && $tipo_count < 3)
					<a href="{!! route('simulacion.facturas',[$user->id,0]) !!}"><i class="fa fa-newspaper nav_icon"></i><span class="nav-label">Simulacion Facturas</span></a>
				@elseif(isset($ctrl) && $tipo_count == 3)
					<a href="{!! route('simulacion.facturas',[$user->id,0]) !!}"><i class="fa fa-newspaper nav_icon"></i><span class="nav-label">Simulacion Facturas</span></a>
				@endif		                        
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(10, $user->id))
			<li>
				<a href="{{route('informes.periodicos.alertas',$user->id)}}"><i class="fa fa-bullhorn nav_icon"></i><span class="nav-label">Informes y Alertas</span></a>
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(11, $user->id))
			<li>
				<a href="{{route('emisiones.co2',$user->id)}}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Emisiones de CO<sub>2</sub></span></a>
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(12, $user->id))
			<li>
				<a href="{{route('exportar.datos',$user_log)}}"><i class="fa fa-download nav_icon"></i><span class="nav-label">Exportar Datos</span></a>
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(14, $user->id))
			<li>
				{{-- @if(!isset($ctrl)) --}}
					<a href="{!! route('analyzersgroup',[$user->id]) !!}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label" >Analizadores Submetering</span></a>
				{{-- @elseif(isset($ctrl)) --}}
					{{-- <a href="{!! route('analyzersgroup',[$user->id,0]) !!}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label" >Analizadores Submetering</span></a> --}}
				{{-- @endif                 --}}
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(15, $user->id))
			<li>
				<a href="{!! route('statistics.resume',['type'=>'produccion','user_id'=>$user->id]) !!}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Producción Submetering</span> </a>
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(16, $user->id))
			<li>
				<a href="{!! route('statistics.resume',['type'=>'indicadores','user_id'=>$user->id]) !!}"><i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Indicadores Energéticos</span> </a>
			</li>
		@endif
		@if(App\Http\Controllers\GroupsController::checkMenu(18, $user->id))
			<li>
				<a href="{!! route('statistics.resume',['type'=>'representacion','user_id'=>$user->id]) !!}"><i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Representación Datos</span> </a>
			</li>
		@endif
		<li>
			<a href="{{route('logaccesos', $user->id)}}"><i class="fa fa-history nav_icon"></i><span class="nav-label">Control de Accesos</span></a>
		</li>
		@if(App\Http\Controllers\GroupsController::checkMenu(13, $user->id))
			<li>
				<a href="{{ route('area.cliente', $user->id) }}"><i class="fa fa-user nav_icon"></i><span class="nav-label">Área cliente</span></a>
			</li>
		@endif
	@endif
	{{-- @if($user_log->tipo == 1) --}}
		<li><a></a></li>
		<li>
			<a href="{{ route('logout') }}" class="logout-button-nav"><i class="fa fa-sign-out-alt nav_icon"></i><span class="nav-label">Salir</span></a>
			<form id="logout-form-menu" action="{{ route('logout') }}" method="POST" style="display: none;">
			  {{ csrf_field() }}
		  </form>
		</li>
		<li></li>
	{{-- @endif --}}
</ul>

<script>
	$(document).ready(function(){
		$(".logout-button-nav").click(function(event){
			event.preventDefault();
			$("#logout-form-menu").submit();
		});                                 
	});
</script> 