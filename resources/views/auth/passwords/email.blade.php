@extends('layouts.newapp')

@section('content')
<h2 class="section-title">Recuperar contrase&ntilde;a</h2>
<div>Ingrese el correo electrónico para enviar el código de validación</div>
@if (session('status'))
	<div class="alert alert-success">{{ session('status') }}</div>
@endif
<form id="myForm" method="POST" action="{{route('cambio.password')}}">
	{{ csrf_field() }}
	@if (session('status'))
		<a class="btn" href='{{url("/")}}'>Cerrar</a>
	@else
		<input id="email" class="input" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="* Email" required>
		@if ($errors->has('email'))
			<span class="form-error">El email especificado no es v&aacute;lido</span>
		@endif
	
		@if ($errors->has('error'))
			<span class="form-error">Error inesperado al enviar el email. Intentelo de nuevo. Si el problema persiste, contacte al administrador.</span>
		@endif
		<input class="btn" type="submit" value="Enviar">
	@endif
</form>
<a class="styled-link" href="{{ route('login') }}">Volver a iniciar sesi&oacute;n</a>
@endsection
