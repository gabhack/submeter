@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">        
            <div class="login" style="padding: 0">             
                <div class="login-bottom">
                    <h2 class="text-center">Completa los siguientes datos</h2>
                    @if (Session::has('message-error'))
                        <div id="message-success" class="alert alert-danger">{{ Session::get('message-error') }}</div>
                    @endif
                    <form novalidate class="form-horizontal" method="POST" action="{{ route('save.registro') }}">
                        {{ csrf_field() }}
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nombre" autofocus>
                                                                        
                                </div>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('apellido') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="apellido" type="text" name="apellido" value="{{ old('apellido') }}" placeholder="Apellido" autofocus>
                                                                        
                                </div>
                                @if ($errors->has('apellido'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('apellido') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="direccion" type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección de la empresa" autofocus>                                  
                                </div>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Correo" autofocus>
                                                                        
                                </div>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="codigo" type="text" name="codigo" value="{{ old('codigo') }}" placeholder="Código" autofocus>                                  
                                </div>
                                @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input iod = "password" type="password" placeholder="Contraseña" name="password">
                                    
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class=" login-do">
                                <label class="hvr-shutter-in-horizontal login-sub">
                                    <input type="submit" value="Enviar">
                                </label>
                            </div>

                        </div>          
                        
                        <div class="clearfix"> </div>
                    </form>
                </div>
            </div>                   
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{asset('js/jquery.nicescroll.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script>
@endsection
