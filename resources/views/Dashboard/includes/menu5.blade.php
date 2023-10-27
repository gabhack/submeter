<nav class="nav" id="nav-menu">
	<ul class="nav__list" id="nav-links">
		@if($user_log->tipo == 1)
			<li class="nav__item"><a href="#!"><i class="fa fa-users"></i><span>Administrar usuarios</span></a></li>
			<li class="nav__item"><a href="{{route('admin.users',[1,0])}}"><i class="fa fa-user-plus"></i><span>Administradores</span></a></li>
			<li class="nav__item"><a href="{{route('admin.users',[2,0])}}"><i class="fa fa-user"></i><span>Clientes</span></a></li>
			<li class="nav__item"><a href="{{route('enterprise.index')}}"><i class="fa fa-building"></i><span>Empresas</span></a></li>
			<li class="nav__item"><a href="{{route('statistics.listAll')}}"><i class="fa fa-building"></i><span>IDENs & Producción</span></a></li>
		@endif 
		@if($user_log->tipo == 2)
			@if(App\Http\Controllers\GroupsController::checkMenu(1, $user->id))
				<li class="nav__item">
					@if(!isset($ctrl) && $tipo_count == 1)
						<a href="{{route('resumen.energia.potencia',$user_log)}}">
							<i class="fa fa-chart-bar"></i><span>Energía y Potencia</span>
						</a>
					@elseif(!isset($ctrl) && $tipo_count == 2)
						<a href="{{route('resumen.energia.potencia',$user_log)}}">
							<i class="fa fa-chart-bar"></i><span>Resumen</span>
						</a>
					@elseif(!isset($ctrl) && $tipo_count == 3)
						<a href="{{route('resumen.energia.potencia',$user_log)}}">
							<i class="fa fa-chart-bar"></i><span>Resumen GN</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 1)
						<a href="{{route('resumen.energia.potencia',$user->id)}}">
							<i class="fa fa-chart-bar"></i><span>Energía y Potencia</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 2)
						<a href="{{route('resumen.energia.potencia',$user->id)}}">
							<i class="fa fa-chart-bar"></i><span>Resumen</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 3)
						<a href="{{route('resumen.energia.potencia',$user->id)}}">
							<i class="fa fa-chart-bar"></i><span>Resumen GN</span>
						</a>
					@endif
				</li>
			@endif
			@if(App\Http\Controllers\GroupsController::checkMenu(2, $user->id))
				<li class="nav__item">
					<a href="
						@if(!isset($ctrl) && ($tipo_count == 1 || $tipo_count == 2 || $tipo_count == 3))
							{{-- {!! route('ver.panel.user',[$user->id, 0]) !!} --}}
							{!! route('ver.panel.user',[$user->id]) !!}
						@elseif(isset($ctrl) && ($tipo_count == 1 || $tipo_count == 2 || $tipo_count == 3))
							{{-- {!! route('ver.panel.user',[$user->id, $ctrl]) !!} --}}
							{!! route('ver.panel.user',[$user->id]) !!}
						@endif
					">
						<i class="far fa-clock "></i><span>Contadores</span>
					</a>
				</li>
			@endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(3, $user->id))
				@if ($tipo_count === 1 || $tipo_count === 2)		
					<li class="nav__item">
						@if(!isset($ctrl) && $tipo_count == 1)
							<a href="{{route('consumo.energia',$user_log)}}">
								<i class="fa fa-chart-bar"></i><span>Consumo de Energía</span>
							</a>
						@elseif(!isset($ctrl) && $tipo_count == 2)
							<a href="{{route('consumo.energia',$user_log)}}">
								<i class="fa fa-chart-bar"></i><span>Consumo y Generación</span>
							</a>
						@elseif(isset($ctrl) && $tipo_count == 1)
							<a href="{{route('consumo.energia',$user->id)}}">
								<i class="fa fa-chart-bar"></i><span>Consumo de Energía</span>
							</a>
						@elseif(isset($ctrl) && $tipo_count == 2)
							<a href="{{route('consumo.energia',$user->id)}}">
								<i class="fa fa-chart-bar"></i><span>Consumo y Generación</span>
							</a>
						@endif
					</li>
				@endif
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(4, $user->id))
				<li class="nav__item">
					<a href="
						@if(!isset($ctrl) && $tipo_count == 1)
							{{route('analisis.potencia2',$user_log)}}
						@elseif(!isset($ctrl) && $tipo_count == 2)
							{{route('analisis.potencia2',$user_log)}}
						@elseif(isset($ctrl) && $tipo_count == 1)
							{{route('analisis.potencia2',$user->id)}}
						@elseif(isset($ctrl) && $tipo_count == 2)
							{{route('analisis.potencia2',$user->id)}}
						@endif
					">
						<i class="fa fa-chart-area"></i><span>Análisis Potencia</span>
					</a>
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(5, $user->id))
				<li class="nav__item">
					@if(!isset($ctrl) && $tipo_count == 1)
						<a href="{{route('simulacion.potencia',$user_log)}}">
							<i class="fa fa-chart-area"></i><span>Simulación Potencia</span>
						</a>
					@elseif(!isset($ctrl) && $tipo_count == 2)
						<a href="{{route('simulacion.potencia',$user_log)}}">
							<i class="fa fa-chart-area"></i><span>Simulación Potencia</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 1)
						<a href="{{route('simulacion.potencia',$user->id)}}">
							<i class="fa fa-chart-area"></i><span>Simulación Potencia</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 2)
						<a href="{{route('simulacion.potencia',$user->id)}}">
							<i class="fa fa-chart-area"></i><span>Simulación Potencia</span>
						</a>
					@endif                	
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(6, $user->id))
				<li class="nav__item">
					@if(!isset($ctrl) && $tipo_count == 1)
						<a href="{{route('mercado.energetico',$user_log)}}">
							<i class="fa fa-chart-area"></i><span>Mercado Energético</span>
						</a>
					@elseif(!isset($ctrl) && $tipo_count == 2)
						<a href="{{route('mercado.energetico',$user_log)}}">
							<i class="fa fa-chart-area"></i><span>Mercado Energético</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 1)
						<a href="{{route('mercado.energetico',$user->id)}}">
							<i class="fa fa-chart-area"></i><span>Mercado Energético</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 2)
						<a href="{{route('mercado.energetico',$user->id)}}">
							<i class="fa fa-chart-area"></i><span>Mercado Energético</span>
						</a>
					@endif                	
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(7, $user->id))
				@if($tipo_count < 3)
					<li class="nav__item">
						<a href="{{route('seguimiento.objetivos',$user_log)}}">
							<i class="fa fa-chart-area"></i><span>Seguimiento Objetivos</span>
						</a>
					</li>
				@endif
		  @endif
			@if(App\Http\Controllers\GroupsController::checkMenu(8, $user->id))
				@if($tipo_count <= 3)
					<li class="nav__item">
						<a href="{{route('comparador.ofertas',$user_log)}}">
							<i class="fa fa-euro-sign"></i><span>Comparador Ofertas</span>
						</a>					
					</li>
				@endif       
			@endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(9, $user->id))
				<li class="nav__item">
					@if(!isset($ctrl) && $tipo_count < 3)
						<a href="{!! route('simulacion.facturas',[$user->id]) !!}">
							<i class="fa fa-newspaper"></i><span>Simulación Facturas</span>
						</a>
					@elseif(!isset($ctrl) && $tipo_count == 3)
						<a href="{!! route('simulacion.facturas',[$user->id]) !!}">
							<i class="fa fa-newspaper"></i><span>Simulación Facturas</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count < 3)
						{{-- <a href="{!! route('simulacion.facturas',[$user->id,0]) !!}"> --}}
						<a href="{!! route('simulacion.facturas',[$user->id]) !!}">
							<i class="fa fa-newspaper"></i><span>Simulacion Facturas</span>
						</a>
					@elseif(isset($ctrl) && $tipo_count == 3)
						{{-- <a href="{!! route('simulacion.facturas',[$user->id,0]) !!}"> --}}
						<a href="{!! route('simulacion.facturas',[$user->id]) !!}">
							<i class="fa fa-newspaper"></i><span>Simulacion Facturas</span>
						</a>
					@endif		                        
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(10, $user->id))
				<li class="nav__item">
					<a href="{{route('informes.periodicos.alertas',$user->id)}}">
						<i class="fa fa-bullhorn"></i><span>Informes y Alertas</span>
					</a>
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(11, $user->id))
				<li class="nav__item">
					<a href="{{route('emisiones.co2',$user->id)}}">
						<i class="fa fa-chart-area"></i><span>Emisiones de CO<sub>2</sub></span>
					</a>
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(12, $user->id))
				<li class="nav__item">
					<a href="{{route('exportar.datos',$user_log)}}">
						<i class="fa fa-download"></i><span>Exportar Datos</span>
					</a>
				</li>
		  @endif		
		  @if(App\Http\Controllers\GroupsController::checkMenu(14, $user->id))
				<li class="nav__item">
					<a href="
						{{-- @if(!isset($ctrl)) --}}
							{!! route('analyzersgroup',[$user->id]) !!}
						{{-- @elseif(isset($ctrl)) --}}
							{{-- {!! route('analyzersgroup',[$user->id,0]) !!} --}}
						{{-- @endif --}}
					">
						<i class="fa fa-chart-area"></i><span>Analizadores Submetering</span>
					</a>                
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(15, $user->id))
				<li class="nav__item">
					<a href="{!! route('statistics.resume',['type'=>'produccion','user_id'=>$user->id]) !!}">
						<i class="fa fa-chart-bar nav_icon"></i><span class="nav-label">Producción Submetering</span>
					</a>
				</li>
		  @endif
		  @if(App\Http\Controllers\GroupsController::checkMenu(16, $user->id))
				<li class="nav__item">
					<a href="{!! route('statistics.resume',['type'=>'indicadores','user_id'=>$user->id]) !!}">
						<i class="fa fa-chart-area nav_icon"></i><span class="nav-label">Indicadores Energéticos</span>
					</a>
				</li>
			@endif
			@if(App\Http\Controllers\GroupsController::checkMenu(18, $user->id))
				<li class="nav__item">
					<a href="{!! route('statistics.resume',['type'=>'representacion','user_id'=>$user->id]) !!}">
						<i class="fa fa-bar-chart nav_icon"></i><span class="nav-label">Representación Datos</span>
					</a>
				</li>
			@endif
		  <li class="nav__item"><a href="{{route('logaccesos', $user->id)}}"><i class="fa fa-history"></i><span>Control de Accesos</span></a></li>		
			@if(App\Http\Controllers\GroupsController::checkMenu(13, $user->id))
				<li class="nav__item">
					<a href="{{ route('area.cliente', $user->id) }}">
						<i class="fa fa-user"></i><span>Área cliente</span>
					</a>
				</li>
			@endif		
			<li class="nav__item"></li>
		  <li class="nav__item"><a href="https://help.submeter.es" target="_blank"><i class="fa fa-file-alt"></i><span>Manual Submetering 4.0</span></a></li>
		@endif
	</ul>
</nav>