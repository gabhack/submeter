@extends('layouts.newapp')

@section('content')
	<h2 class="section-title">Restablecer Contraseña</h2>
	<form id="myForm" method="POST" action="{{ route('reset.password.login') }}">
		{{ csrf_field() }}
		<input type="hidden" name="token" value="{{ $token }}">
		<label for="email" class="label">Correo Electrónico</label>
		<input id="email" class="input" type="email" name="email" value="{{ $email or old('email') }}" placeholder="* Email" required autofocus>
		@if ($errors->has('email'))
			<span class="form-error">
				@if($errors->first('email') == 'passwords.token')
					Token Invalido
				@else
					Email invalido
				@endif
			</span>
		@endif
		<label for="password" class="label">Nueva Contraseña</label>
		<input id="password" class="input" type="password" name="password" placeholder="* Nueva contrase&ntilde;a" required>
		@if ($errors->has('password'))
			<span class="form-error">Su contraseña debe tener 6 carácteres como mínimo y debe ser confirmada.</span>
		@endif
		<label for="password-confirm" class="label">Confirmar Contraseña</label>
		<input id="password-confirm" class="input" type="password" name="password_confirmation" placeholder="* Confirmar contrase&ntilde;a" required>
		@if ($errors->has('password_confirmation'))
			<span class="form-error">{{ $errors->first('password_confirmation') }}</span>
		@endif
		<input class="btn" type="submit" value="Restablecer Contrase&ntilde;a">
	</form>
@endsection
