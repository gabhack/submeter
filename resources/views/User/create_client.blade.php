@extends('Dashboard.layouts.global')

@section('content')

	<div id="wrapper">        
        <div id="page-wrapper" class="gray-bg dashbard-1">
       		<div class="content-main">	
				<div class="content-mid" >					
					<div class="grid_3 col-md-12" style="height: 500%;">
						<h3 class="head-top">Registrar cliente</h3>

						@if (Session::has('message'))
			                <div id="message-success" class="alert alert-success">{{ Session::get('message') }}</div>
			            @endif

			            @if (Session::has('message-error'))
			                <div id="message-success" class="alert alert-danger">{{ Session::get('message-error') }}</div>
			            @endif


						<form class="form-horizontal" name="registrar_user" method="POST" autocomplete="off" novalidate action="{{ route('store.users') }}">
	                        {{ csrf_field() }}

							<input id="tipo" type="hidden" class="form-control" name="tipo" value="2" required autofocus>

	                        <div id="name_div" class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
	                            <label for="name" class="col-md-1 control-label">Organización</label>

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

	                        <div id="contadores_div" class="form-group{{ $errors->has('contadores') ? ' has-error' : '' }}">
	                            <label for="contadores" class="col-md-1 control-label">Contadores</label>

	                            <div class="col-md-2">
	                                <input id="contadores" type="number" min="1" class="form-control" name="contadores" value="{{ old('contadores') }}" required>

	                                @if ($errors->has('contadores'))
	                                    <span class="help-block">
	                                        <strong>{{ $errors->first('contadores') }}</strong>
	                                    </span>
	                                @endif
	                            </div>
	                            <div class="col-md-1">
	                            	<button type="button" class="btn btn-success" style="width: 100%;" onClick="configurar();">Configurar</button>
	                            </div>
	                        </div>

	                        <div id="config_contadores" class="form-group">

	                        </div>


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
	            <p> &copy; 2020 Submeter 4.0. Todos los derechos reservados</p>
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

  		function configurar() {
  			var num_counts = $('#contadores').val();
  			var count = $('#name').val();
  			for (var k = 0; k < num_counts; k++) {
  				j = k;
  				while($('#id_contador'+j).length)
  				{
  					j++;
  				}  				
  				i = j;
  				$('<div id="id_contador'+i+'"><div class = "col-md-12 text-center"><h3 style="color:#004165;">Configuración de Contador '+(i+1)+'</h3><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_cont'+i+'" name = "name_cont'+i+'" class="form-control"></input></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host'+i+'" name = "val_host'+i+'" class="form-control"></input></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase'+i+'" name = "val_dbase'+i+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port'+i+'" name = "val_port'+i+'" class="form-control"></input></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username'+i+'" name = "val_username'+i+'" class="form-control"></input></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password'+i+'" name = "val_password'+i+'" class="form-control"></input></div><br><br></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Tipo: </label><div class="col-md-3"><select id = "tipo'+i+'" name = "tipo'+i+'" class="form-control"><option value=1>Consumo</option><option value=2>Generación</option><option value=3>Gas</option></select></div><label class="col-md-1 control-label">Analizadores: </label><div class="col-md-1"><input id=analizadores_'+i+' type="number" min="0" class="form-control" name=analizadores_'+i+' value="{{ old("analizadores") }}"></div><div class="col-md-1"><button type="button" class="btn btn-success" style="width: 100%;" onClick="configurarAnalizador('+i+');">Configurar<br>Analizador</button></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarContador('+i+');">Borrar<br>Contador</button></div><br><br><br></div><div id=analizadores'+i+'></div></div>').appendTo('#config_contadores');
  			}  		    
  		}

  		function configurarAnalizador(id) {  			
  			var num_counts = $('#analizadores_'+id).val();
  			console.log(num_counts);
  			var count = $('#name').val();
  			for (var k = 0; k < num_counts; k++) {
  				j = k;
  				while($('#id_analizador'+j+'_contador_'+(id+1)+'').length)
  				{
  					j++;  					
  				}  				
  				i = j;

  				if(i == 0)
  				{
  					$('<div id="id_analizador'+i+'_contador_'+(id+1)+'" class = "col-md-12 text-center"><div class = "col-md-12 text-center"><h4 style="color:#272822">Configuración del Analizador Principal'+' (Contador '+(id+1)+')</h4><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_analizador'+i+'_contador_'+(id+1)+'" name = "name_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host_analizador'+i+'_contador_'+(id+1)+'" name = "val_host_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" name = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port_analizador'+i+'_contador_'+(id+1)+'" name = "val_port_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username_analizador'+i+'_contador_'+(id+1)+'" name = "val_username_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password_analizador'+i+'_contador_'+(id+1)+'" name = "val_password_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Color etiqueta: </label><div class="col-sm-1"><input id = "val_color_analizador'+i+'_contador_'+(id+1)+'" name = "val_color_analizador'+i+'_contador_'+(id+1)+'" class="form-control" type = "color"></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarAnalizador('+i+','+id+');">Borrar<br>Analizador</button></div><br><br><br><br><br></div></div>').appendTo('#analizadores'+id);  					
  				}else{
  					$('<div id="id_analizador'+i+'_contador_'+(id+1)+'" class = "col-md-12 text-center"><div class = "col-md-12 text-center" id="id_analizador'+i+'_contador_'+(id+1)+'"><h4 style="color:#272822">Configuración del Analizador '+(i+1)+' (Contador '+(id+1)+')</h4><br><br></div><div class="col-md-12"><label class="col-md-1 control-label">Nombre: </label><div class="col-md-3"><input id = "name_analizador'+i+'_contador_'+(id+1)+'" name = "name_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Host: </label><div class="col-md-3"><input id = "val_host_analizador'+i+'_contador_'+(id+1)+'" name = "val_host_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">DataBase: </label><div class="col-md-3"><input id = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" name = "val_dbase_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Port: </label><div class="col-md-2"><input id = "val_port_analizador'+i+'_contador_'+(id+1)+'" name = "val_port_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Username: </label><div class="col-md-3"><input id = "val_username_analizador'+i+'_contador_'+(id+1)+'" name = "val_username_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></input></div><label class="col-md-1 control-label">Password: </label><div class="col-md-3"><input id = "val_password_analizador'+i+'_contador_'+(id+1)+'" name = "val_password_analizador'+i+'_contador_'+(id+1)+'" class="form-control"></div><div class = "col-md-12"><br><br><label class="col-md-1 control-label">Color etiqueta: </label><div class="col-sm-1"><input id = "val_color_analizador'+i+'_contador_'+(id+1)+'" name = "val_color_analizador'+i+'_contador_'+(id+1)+'" class="form-control" type = "color"></div><div class="col-md-1"><button type="button" class="btn btn-danger" style="width: 100%;" onClick="borrarAnalizador('+i+','+id+');">Borrar<br>Analizador</button></div><br><br><br><br><br></div></div>').appendTo('#analizadores'+id);
  				}
  			}  		    
  		}

  		function borrarAnalizador(id_A,id_C)
  		{  			
  			console.log('#id_analizador'+id_A+'_contador_'+(parseInt(id_C)+1));
  			$('#id_analizador'+id_A+'_contador_'+(parseInt(id_C)+1)).remove();
  		}

  		function borrarContador(id_C)
  		{  			
  			$('#id_contador'+id_C).remove();
  		}

	</script>

@endsection