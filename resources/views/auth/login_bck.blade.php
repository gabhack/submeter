@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">       
            <div class="login">    
                @include('auth.alertas.cuenta_bloqueada')          
                <div class="login-bottom">
                    @if (Session::has('message-error'))
                        <div id="message-danger" class="alert alert-danger">{{ Session::get('message-error') }}</div>
                    @endif
                    <div class="col-md-12">
                        <div class="col-md-6" style="margin: auto">
                            <!-- <h2>Ingresar</h2> -->
                        </div>
                        <div class="col-md-3"></div>                        
                        <div class="col-md-3">
                            <img src="{{asset('images/submeter_final-01.png')}}" class="img-responsive">
                        </div>
                    </div>                    
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}" id="myform">
                        {{ csrf_field() }}
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                                    <i class="fa fa-envelope"></i>                                    
                                </div>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="login-mail">
                                    <input iod = "password" type="password" placeholder="Contraseña" required name="password">
                                    <i class="fa fa-lock"></i>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <!-- {!! route('password.request') !!} -->
                            <a class="news-letter " href="{{url('password/reset')}}">
                                <label class="checkbox1">¿Olvidaste tu contraseña?</label>
                            </a>                            
                        </div>          
                        <div class="col-md-6 login-do">
                            <label class="hvr-shutter-in-horizontal login-sub">
                                <a href="#" onclick="document.getElementById('myform').submit()">Ingresar</a>
                            </label>
                            <p>¿No tienes cuenta? Solicita tu registro</p>
                            <a href="{!! route('solicitud.registro') !!}" class="hvr-shutter-in-horizontal">Solicitud de registro</a>
                        </div>                        
                        <div class="clearfix"> </div>
                    </form>                    
                </div>
            </div>                   

        {{--<div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>--}}
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{asset('js/jquery.nicescroll.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script>
@endsection
