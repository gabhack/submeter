@extends('layouts.newapp')

@section('content')        	
	@if (Session::has('message'))
		<div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
	@endif
	{{-- <h2>Solicita tu registro en Submeter 4.0</h2> --}}
	<h2 class="section-title">Solicitud de registro</h2>
	<form id="myForm" method="POST" action="{{ route('send.solicitud.registro') }}">
		{{ csrf_field() }}
		<input id="nombre_prospecto" class="input" type="text" name="nombre_prospecto" value="{{ old('nombre_prospecto') }}" placeholder="* Nombre" required autofocus>
		@if ($errors->has('nombre_prospecto'))
			<span class="form-error">{{ $errors->first('nombre_prospecto') }}</span>
		@endif
		<input id="apellido_prospecto" class="input" type="text" name="apellido_prospecto" value="{{ old('apellido_prospecto') }}" placeholder="* Apellidos" required autofocus>
		@if ($errors->has('apellido_prospecto'))
			<span class="form-error">{{ $errors->first('apellido_prospecto') }}</span>
		@endif
		<input id="empresa_prospecto" class="input" type="text" name="empresa_prospecto" value="{{ old('empresa_prospecto') }}" placeholder="* Empresa" required autofocus>
		@if ($errors->has('empresa_prospecto'))
			<span class="form-error">{{ $errors->first('empresa_prospecto') }}</span>
		@endif
		<input id="correo_prospecto" class="input" type="email" name="correo_prospecto" value="{{ old('correo_prospecto') }}" placeholder="* Correo" required autofocus>
		@if ($errors->has('correo_prospecto'))
			<span class="form-error">{{ $errors->first('correo_prospecto') }}</span>
		@endif
		<input id="telefono_prospecto" class="input" type="text" name="telefono_prospecto" value="{{ old('telefono_prospecto') }}" placeholder="* Teléfono" required autofocus>
		@if ($errors->has('telefono_prospecto'))
			<span class="form-error">{{ $errors->first('telefono_prospecto') }}</span>
		@endif				
		<select id="tipo_monitorizacion" class="input" name="tipo_monitorizacion" required autofocus>
			<option value=1>Contadores Eléctricos</option>
			<option value=2>Contadores de Gas</option>
			<option value=3>Analizadores Eléctricos</option>
			<option value=4>Producción Scada | ERP</option>
			<option value=5>Otros</option>
		</select>                                    
		@if ($errors->has('tipo_monitorizacion'))
			<span class="form-error">{{ $errors->first('tipo_monitorizacion') }}</span>
		@endif
		<input class="btn" type="submit" value="Enviar">
	</form>            		
	<span>¿Ya tienes una cuenta?</span>
	<a href="{{ route('login') }}" class="btn">Iniciar sesi&oacute;n</a>
@endsection

@section('scripts')
	{{-- <script src="{{asset('js/jquery.nicescroll.js')}}"></script> --}}
	{{-- <script src="{{asset('js/scripts.js')}}"></script> --}}
@endsection