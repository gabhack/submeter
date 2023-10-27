@extends('Dashboard.layouts.global5')

@section('content')
	@php
		$titulo = "Editar perfil";
	@endphp
	<div class="row">
		<div class="column">
			@if (Session::has('message'))
				<div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			@endif

			{!! Form::open(['route' => ['store.perfil', Auth::user()->id], 'method' => 'POST', 'files' => true, 'novalidate', 'id' => 'perfil_user', 'class' => 'form']) !!}

				<div class="form-group">
					<label class="form-label" for="direccion">Dirección</label>
					<input class="form-control" id="direccion" type="text" name="direccion" value="{{ $perfil->direccion }}" required autofocus>
					@if ($errors->has('direccion'))
						<p>
							<strong>{{ $errors->first('direccion') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="fijo" class="col-md-1 control-label text-center">Teléfono Fijo</label>
					<input class="form-control" id="fijo" type="text" name="fijo" value="{{ $perfil->fijo }}" required autofocus>
					@if ($errors->has('fijo'))
						<p>
							<strong>{{ $errors->first('fijo') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="movil">Teléfono Móvil</label>
					<input class="form-control" id="movil" type="text" name="movil" value="{{ $perfil->movil }}" required autofocus>
					@if ($errors->has('movil'))
						<p>
							<strong>{{ $errors->first('movil') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="denominacion_social">Denominación Social</label>		
					<input class="form-control" id="denominacion_social" type="text" name="denominacion_social" value="{{ $perfil->denominacion_social }}" required autofocus>		
					@if ($errors->has('denominacion_social'))
						<p>
							<strong>{{ $errors->first('denominacion_social') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="domicilio_social">Domicilio Social</label>		
					<input class="form-control" id="domicilio_social" type="text" name="domicilio_social" value="{{ $perfil->domicilio_social }}" required autofocus>
					@if ($errors->has('domicilio_social'))
						<p>
							<strong>{{ $errors->first('domicilio_social') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
						<label class="form-label" for="domicilio_suministro">Domicilio del suministro</label>		
						<input class="form-control" id="domicilio_suministro" type="text" name="domicilio_suministro" value="{{ $perfil->domicilio_suministro }}" required autofocus>
						@if ($errors->has('domicilio_suministro'))
							<p>
								<strong>{{ $errors->first('domicilio_suministro') }}</strong>
							</p>
						@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="cups">CUPS</label>		
					<input class="form-control" id="cups" type="text" name="cups" value="{{ $perfil->cups }}" required autofocus>		
					@if ($errors->has('cups'))
						<p>
							<strong>{{ $errors->first('cups') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="cif">CIF</label>		
					<input class="form-control" id="cif" type="text" name="cif" value="{{ $perfil->cif }}" required autofocus>
					@if ($errors->has('cif'))
						<p>
							<strong>{{ $errors->first('cif') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" style="padding-left: 2px;" for="empresa_distribuidora">Empresa Distribuidora</label>		
					<input class="form-control" id="empresa_distribuidora" type="text" name="empresa_distribuidora" value="{{ $perfil->empresa_distribuidora }}" required autofocus>
					@if ($errors->has('empresa_distribuidora'))
						<p>
							<strong>{{ $errors->first('empresa_distribuidora') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="empresa_comercializadora">Empresa Comercializadora</label>		
					<input class="form-control" id="empresa_comercializadora" type="text" name="empresa_comercializadora" value="{{ $perfil->empresa_comercializadora }}" required autofocus>
					@if ($errors->has('empresa_comercializadora'))
						<p>
							<strong>{{ $errors->first('empresa_comercializadora') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="persona_contacto">Persona de contacto</label>		
					<input class="form-control" id="persona_contacto" type="text" name="persona_contacto" value="{{ $perfil->persona_contacto }}" required autofocus>
					@if ($errors->has('persona_contacto'))
						<p>
							<strong>{{ $errors->first('persona_contacto') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="tarifa">Tarifa</label>	
						<input class="form-control" id="tarifa" type="text" name="tarifa" value="{{ $perfil->tarifa }}" required autofocus>
						@if ($errors->has('tarifa'))
							<p>
								<strong>{{ $errors->first('tarifa') }}</strong>
							</p>
						@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="avatar">Imagen</label>		
					<input class="form-control" id="avatar" type="file" name="avatar" value="{{ $perfil->avatar }}" required>
					@if ($errors->has('avatar'))
						<p>
							<strong>{{ $errors->first('avatar') }}</strong>
						</p>
					@endif
				</div>
		
				<div class="form-group">
					<label class="form-label" for="password">Contraseña</label>
					<input class="form-control" id="password" type="password" name="password" value="{{ $perfil->password }}">
					@if ($errors->has('password'))
						<p>
							<strong>{{ $errors->first('password') }}</strong>
						</p>
					@endif
				</div>		

				<div class="form-group">				
					<label class="form-label" for="repeat">Repetir Contraseña</label>
					<input class="form-control" id="repeat" type="password" name="repeat" value="{{ $perfil->repeat }}">
					@if ($errors->has('repeat'))
						<p>
							<strong>{{ $errors->first('repeat') }}</strong>
						</p>
					@endif
				</div>		

				<input id="user_id" type="hidden" name="user_id" value="{{ Auth::user()->id }}" required >

				<div class="btn-container center">
					<button type="submit" class="btn">Guardar</button>
				</div>	                    
			{!! Form::close() !!}
		</div>
	</div>			
@endsection

@section('scripts')

<script src="{{asset('js/jquery.min.js')}}"> </script>
	<script src="{{asset('js/jquery.metisMenu.js')}}"></script>
	{{-- <script src="{{asset('js/jquery.slimscroll.min.js')}}"></script>         --}}
	<script src="{{asset('js/custom.js')}}"></script>
	<script src="{{asset('js/screenfull.js')}}"></script>
	{{-- <script src="{{asset('js/scripts.js')}}"></script> --}}
	{{-- <script src="{{asset('js/jquery.nicescroll.js')}}"></script>	 --}}
	<script src="{{asset('js/bootstrap.min.js')}}"> </script>
	<script src="{{asset('js/pie-chart.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/skycons.js')}}"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>	
@endsection