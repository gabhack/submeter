@extends('Dashboard.layouts.global')

@section('content')

	<div id="wrapper">        
        <div id="page-wrapper" class="gray-bg dashbard-1">
       		<div class="content-main">	
				<div class="content-mid" >					
					<div class="grid_3 col-md-12" style="height: 500%;">
						<h3 class="head-top">Registrar administrador</h3>

						@if (Session::has('message'))
			                <div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			            @endif

			            @if (Session::has('message-error'))
			                <div id="message-success" class="alert alert-danger">{{ Session::get('message-error') }}</div>
			            @endif


						<form class="form-horizontal" name="registrar_user" method="POST" autocomplete="off" novalidate action="{{ route('store.users') }}">
	                        {{ csrf_field() }}

							<input id="tipo" type="hidden" class="form-control" name="tipo" value="1" required autofocus>

	                        <div id="name_div" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	                            <label for="name" class="col-md-1 control-label">Nombre</label>

	                            <div class="col-md-11">
	                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

	                                @if ($errors->has('name'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('name') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>

	                        <div id="email_div" class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
	                            <label for="email" class="col-md-1 control-label">Correo</label>

	                            <div class="col-md-11">
	                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

	                                @if ($errors->has('email'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('email') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div>

	                        <div id="password_div" class="form-group{{ $errors->has('password') ? ' has-error' : '' }} password">
	                            <label for="password" class="col-md-1 control-label">Contraseña</label>

	                            <div class="col-md-9">
	                                <input id="password" readonly type="text" class="form-control" name="password" required>

	                                @if ($errors->has('password'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('password') }}</strong>
	                                    </span>
	                                @endif
	                            </div>

	                            <div class="col-md-2">
	                            	<button type="button" class="btn btn-default" style="width: 100%;" onClick="generate();">Generar</button>
	                            </div>
	                        </div>

	                        <!-- <div id="contadores_div" class="form-group{{ $errors->has('contadores') ? ' has-error' : '' }}">
	                            <label for="contadores" class="col-md-1 control-label">Contadores</label>

	                            <div class="col-md-2">
	                                <input id="contadores" type="number" min="1" class="form-control" name="contadores" value="{{ old('contadores') }}" required>

	                                @if ($errors->has('contadores'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('contadores') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                        </div> -->


	                        <div class="form-group">
	                            <div class="col-md-11 col-md-offset-4">
	                                <button type="submit" class="btn btn-primary">
	                                    Crear
	                                </button>
	                            </div>
	                        </div>
	                    </form>
					</div>
				</div>
			</div>
			<div class="copy">
	            <p> &copy; 2017 3Seficiencia. Todos los derechos reservados</p>
			</div>
		</div>
	</div>

@endsection

@section('scripts')

	<script type="text/javascript">
		function randomPassword(length) {
  		    var chars = "abcdefghijklmnopqrstuvwxyz!#$%&*ABCDEFGHIJKLMNOP1234567890";
  		    var pass = "";
  		    for (var x = 0; x < length; x++) {
  		        var i = Math.floor(Math.random() * chars.length);
  		        pass += chars.charAt(i);
  		    }
  		    return pass;
  		}       

  		function generate() {
  		    registrar_user.password.value = randomPassword(8);
  		}

  		// function funcChange(){
  		// 	var selectBox = document.getElementById("tipo_user");
		  //   var tipo_user = selectBox.options[selectBox.selectedIndex].value;

		  //   if (tipo_user == 1) {
		  //   	$('#contadores_div').hide();
		  //   	$('#contadores').attr('disabled',true);
		  //   	$('#name_div label').text('Nombre');
		  //   }else{
		  //   	$('#contadores_div').show();
		  //   	$('#contadores').attr('disabled',false);
		  //   	$('#name_div label').text('Empresa');	
		  //   }
  		// }


	</script>

@endsection