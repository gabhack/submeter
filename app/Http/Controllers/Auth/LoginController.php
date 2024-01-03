<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User; //[Rogelio R - Workana] - Se agrega al controlador para la actualización de datos de la tabla de usuario
use App\AccessLog; //[Rogelio R -Workana] - Se agrega al controlador para la creación de los registros de accesos
use App\Mail\distinctip_notification; //[Rogelio R -Workana] - Se agrega al controlador para el envío de correos con IP diferente a las últimas
use App\Mail\LockOutNotification; //[Rogelio R -Workana] - Se agrega al controlador para el envío de correos cuando la cuenta pasa a LOCKOUT
use App\Mail\LockedNotification; //[Rogelio R -Workana] - Se agrega al controlador para el envío de correos cuando la cuenta pasa a LOCKED
use Carbon\Carbon; //[Rogelio R -Workana] - Se agrega al controlador para el envío de correos cuando
use Illuminate\Support\Facades\Mail; //[Rogelio R -Workana] - Se agrega al controlador para el soporte de envío de email desde el mismo
use App\Http\Controllers\SmsSendMessage; //[Rogelio R -Workana] - Se agrega al controlador para el soporte de envío de SMS
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    //[Rogelio R - Workana]
    //Se agregan las variables al controlador para definir la cantidad de intento antes del bloqueo de login 
    protected $maxAttempts = 3;
    protected $decayMinutes = 1;
    protected $lockoutFlag = 0;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * [Rogelio R - Workana]
     * Se sobreescribe la función de Login para permitir el llamado de la función
     * de deslogueo de otros dispositivos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (is_null($user)) {
            return $this->sendFailLoginResponse($request);
        }

        //Se valida el estatus de la cuenta, si esta en (LOCKOUT) se activa la bandera y se cambia el número máximo de intentos
        if (($this->userLockStatus($request->email) == 'LOCKOUT') && ($this->limiter()->availableIn($this->throttleKey($request)) <= 0)) {
            $this->lockoutFlag = 1;
            $this->maxAttempts = 2;
        }

        //Valida que la cuenta de usuario no esté en bloqueada (LOCKED), si la cuenta está bloqueada lo informa en la página de login
        if ($this->UserLockedValidate($request->email)) {

            $this->AccessLogRecord($request, 'ISLOCKED');

            return $this->sendLockedLoginResponse($request);
        }

        //Si la conexión es satisfactoria
        if ($this->attemptLogin($request)) {
            //Se realiza el deslogueo de las demás sesiones [Rogelio R - Workana]
            $this->logoutOtherDevices($request->password);

            $access = (is_null($user->phone_number)) ? 'NOVALIDATEACCESS1' : 'ACCESS';
            //Se almacena el acceso en el log
            $this->AccessLogRecord($request, $access);

            //Se establece el lock_status en UNLOCKED
            User::where('email', $request->email)->update(['lock_status' => 'UNLOCKED']);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        //Si ha sobrepasado el número de intentos
        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            //Si es la primera vez que alcanza el máximo de intentos, pasa la cuenta a LOCKOUT y envía un correo con la notificación
            if ($this->lockoutFlag == 0) {
                Mail::to($request->email)->cc(["oscar@ingenieros.es"])->send(new LockOutNotification($request->ip(), $request->address, Carbon::now()->addMinutes($request->timezoneoffset * -1)));
                User::where('email', $request->email)->update(['lock_status' => 'LOCKOUT']);
                $this->SendSmsMessage($request->email, str_replace(".", " ", 'Plataforma Submeter\nSe detectaron accesos incorrectos desde la IP: ' . $request->ip() . ' con la ubicación: ' . $request->address));
                $this->AccessLogRecord($request, 'LOCKOUT');
            } else { //Al ser la segunda vez que alcanza el máximo de intentos bloquea la cuenta (LOCKED) e informa al usuario
                $accesos = AccessLog::where('user_email', $request->email)
                    ->where('created_at', '>=', Carbon::now()->addMinutes(-30))
                    ->whereIn('access_status', ['DENIED', 'LOCKOUT'])
                    ->get();

                Mail::to($request->email)->send(new LockedNotification($request->ip(), $request->address, $accesos));
                User::where('email', $request->email)->update(['lock_status' => 'LOCKED']);
                $this->AccessLogRecord($request, 'LOCKED');
                $this->SendSmsMessage($request->email, str_replace(".", " ", 'Plataforma Submeter\nSu cuenta ha sido bloqueada\nIP: ' . $request->ip() . ' con la ubicación: ' . $request->address));
                return $this->sendLockedLoginResponse($request); //Se envía la notificación de que su cuenta ha sido bloqueada
            }

            //Retorna la respuesta por bloqueo de login
            return $this->sendLockoutResponse($request);
        }

        //Almacena en el log el intento fallido
        $this->AccessLogRecord($request, 'DENIED');

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * [Rogelio R - Workana]
     * Se sobreescribe el método de logout con la finalidad de almacenar la fecha de salida
     * 
     */
    public function logout(Request $request)
    {
        $this->AccessLogLogOut($request); //Actualiza el último registro del log de acceso del usuario

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * [Rogelio R - Workana]
     * Se sobreescribe el método de sendLoginResponse con la finalidad de que siempre redireccione al /home
     * después de loguearse satisfactoriamente
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return redirect()->route('home');
    }


    /**
     * [Rogelio R - Workana]
     * Invalida las sesiones del usuario en otros dispositivos o navegadores
     * Copiado de vendor\laravel\framework\src\Illuminate\Auth\SessionGuard.php
     * en Laravel 7.5
     *
     * Utiliza el AuthenticateSession middleware por lo que debe descomentarse 
     * la línea "\Illuminate\Session\Middleware\AuthenticateSession::class" en
     * app\Http\Kernel.php
     *
     * @param  string  $password
     * @param  string  $attribute
     * @return null|bool
     */
    public function logoutOtherDevices($password, $attribute = 'password')
    {
        if (!Auth::user()) {
            return;
        }

        return tap(Auth::user()->forceFill([
            $attribute => Hash::make($password),
        ]))->save();
    }

    /**
     * [Rogelio R - Workana]
     * 
     * Se sobreescribe la funcion sendFailedLoginResponse de la clase "vendor\laravel\framework\src\Illuminate\Foundation\Auth\AuthenticatesUsers.php"
     * con la finalidad de personalizar el mensaje de intentos disponibles en caso de fallo en la contraseña.
     * 
     * La función $this->throttleKey($request) retorna la llave formada por el username e IP de la sesión
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        //Se agrega una variable para obtener los intentos de restantes
        $intentos = $this->limiter()->retriesLeft($this->throttleKey($request), $this->maxAttempts());

        //Si tiene intentos fallidos muestra el mensaje con la cantidad de intentos restantes antes del bloqueo temporal, de lo contrario
        //muestra el mensaje por default.
        $mensaje = trans('auth.remain_attempts', ['attempts' => $intentos]);

        $errors = [$this->username() => $mensaje];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * [Rogelio R - Workana]
     * 
     * Se agrega una función que obtiene el estatus de bloqueo de la cuenta con la finalidad de establecer el control correspondiente
     * a si la cuenta está bloqueada temporalmente (LOCKOUT), bloqueada de forma permanente (LOCKED) o desbloqueada (UNLOCKED)
     */
    protected function userLockStatus($username)
    {
        $usuario = User::where('email', $username)->first();

        if ($usuario != null) {
            return $usuario->lock_status;
        } else {
            return 'UNLOCKED';
        }
    }

    /**
     * [Rogelio R - Workana]
     * 
     * Funcion que valida si la cuenta se encuentra en estatus bloqueado (LOCKED)
     */
    protected function UserLockedValidate($username)
    {
        if ($this->userLockStatus($username) == 'LOCKED') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [Rogelio R - Workana]
     * 
     * Funcion que envía la respuesta cuando la cuenta se encuentra en estatus bloqueado (LOCKED)
     */
    protected function sendLockedLoginResponse(Request $request)
    {
        $mensaje = 'Su cuenta ha sido bloqueada por seguridad';

        $errors = [$this->username() => $mensaje];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors)
            ->with('locked', 'LOCKED'); //Se agrega una var de sesion
    }

    protected function sendFailLoginResponse(Request $request)
    {
        $mensaje = 'Datos de acceso erroneos';

        $errors = [$this->username() => $mensaje];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    protected function sendFailVerificationLoginResponse(Request $request)
    {
        $mensaje = 'Codigo de verificacion erroneo.';

        $errors = [$this->username() => $mensaje];

        $this->guard()->logout();

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * [Rogelio R - Workana]
     * Función que crea un registro en el log de accesos
     */
    protected function AccessLogRecord($request, $status)
    {
        //Si el acceso es satisfactorio, se validan las IP de los registros contra la IP actual, si no existen registros con esa IP se informa al usuario.
        Log::info($status);
        if ($status == "ACCESS") {
            $accesos = AccessLog::where('user_email', $request->email)
                ->where('ip_address', $request->ip())
                ->where('access_status', $status)
                ->where('validated_access', 'NO')
                ->whereNotNull('verification_code')
                ->orderBy('id', 'DESC')
                ->first();

            if (!is_null($accesos)) {
                if (!is_null($accesos->verification_code) && $accesos->verification_code == $request->code) {
                    $accesos->validated_access = 'SI';
                    $accesos->save();
                } else {
                    $accesos->access_status = 'ACCESSERROR';
                    $accesos->save();

                    try {
                        $this->SendSmsMessage($request->email, 'Seguridad Submeter\nUsuario: ' . $request->email . ' Código de verificación erroneo.');
                    } catch (\Exception $e) {
                        Log::error($e);
                    }

                    $this->sendFailVerificationLoginResponse($request);
                }
            } else {

                //Si la última entrada no tiene fecha de salida, se le pone la fecha actual
                $accessLogRecord = AccessLog::where('user_email', $request->email)->latest()->first();
                if ($accessLogRecord) {
                    $localtime = $accessLogRecord->local_timezone_offset;
                    $accessLogRecord->local_logout_date = Carbon::now()->addMinutes(($localtime * -1));
                    $accessLogRecord->save();
                }

                //Se almacena el log de acceso el registro de acceso
                AccessLog::create([
                    'user_email' => $request->email,
                    'ip_address' => $request->ip(),
                    'address_address' => $request->address,
                    'address_latitude' => $request->lat,
                    'address_longitude' => $request->lon,
                    'access_status' => $status,
                    'validated_access' => 'SI',
                    'local_access_date' => Carbon::now()->addMinutes($request->timezoneoffset * -1),
                    'local_timezone_offset' => $request->timezoneoffset
                ]);
            }

        } else {

            //Si la última entrada no tiene fecha de salida, se le pone la fecha actual
            $accessLogRecord = AccessLog::where('user_email', $request->email)->latest()->first();
            if ($accessLogRecord) {
                $localtime = $accessLogRecord->local_timezone_offset;
                $accessLogRecord->local_logout_date = Carbon::now()->addMinutes(($localtime * -1));
                $accessLogRecord->save();
            }
            $validated = ($status == 'NOVALIDATEACCESS2') ? 'NO' : 'SI';
            //Se almacena el log de acceso el registro de acceso
            AccessLog::create([
                'user_email' => $request->email,
                'ip_address' => $request->ip(),
                'address_address' => $request->address,
                'address_latitude' => $request->lat,
                'address_longitude' => $request->lon,
                'access_status' => $status,
                'validated_access' => $validated,
                'local_access_date' => Carbon::now()->addMinutes($request->timezoneoffset * -1),
                'local_timezone_offset' => $request->timezoneoffset
            ]);

        }

    }

    /**
     * [Rogelio R - Workana]
     * Función que actualiza el último registro del usuario con su fecha de salida
     */
    protected function AccessLogLogOut($request)
    {
        $user = Auth::user();
        if ($user) {
            $accessLogRecord = AccessLog::where('user_email', $user->email)->latest()->first();
            if ($accessLogRecord) {
                $localtime = $accessLogRecord->local_timezone_offset;
                $accessLogRecord->local_logout_date = Carbon::now()->addMinutes(($localtime * -1));
                $accessLogRecord->save();
            }
        }
    }

    protected function SendSmsMessage($email, $message)
    {
        $user = User::where('email', $email)->first();
        SmsSendMessage::SendMessage($user->phone_number, $message);
    }

    public function validateLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (is_null($user)) {
            echo json_encode([
                'result' => 0,
                'message' => 'Datos de acceso erroneos'
            ]);

            return;
        }

        if ($user->lock_status == "LOCKED") {
            echo json_encode([
                'result' => 0,
                'message' => 'Usuario bloqueado'
            ]);

            return;
        }

        //Si la conexión es satisfactoria, las credenciales son correctas
        if (password_verify($request->password, $user->password)) {
            $accesos = AccessLog::where('user_email', $request->email)
                ->where('ip_address', $request->ip())
                ->where('access_status', 'ACCESS')
                ->where('validated_access', 'SI')
                ->count();


            if ($accesos == 0) {

                $randomNumber = random_int(100000, 999999);
                $existe = AccessLog::where('user_email', $request->email)
                    ->where('ip_address', $request->ip())
                    ->count();
                try {

                    if ($existe == 0) {

                        Mail::to($request->email)->cc(["oscar@ingenieros.es"])
                            ->send(
                                new distinctip_notification(
                                    $request->ip(),
                                    $request->address,
                                    Carbon::now()->addMinutes($request->timezoneoffset * -1)
                                )
                            );

                        if (!is_null($user->phone_number)) {
                            $this->SendSmsMessage(
                                $request->email,
                                'Plataforma Submeter\nAcceso desde una nueva IP: ' . $request->ip() . ' con la ubicación: ' . $request->address
                            );
                        }
                    }


                    if (!is_null($user->phone_number)) {

                        $this->SendSmsMessage(
                            $request->email,
                            'Seguridad Submeter\nUsuario: ' . $request->email . ' Código Verificación: ' . $randomNumber
                        );

                        //Se almacena el log de acceso el registro de acceso con el codigo generado
                        AccessLog::create([
                            'user_email' => $request->email,
                            'ip_address' => $request->ip(),
                            'address_address' => $request->address,
                            'address_latitude' => $request->lat,
                            'address_longitude' => $request->lon,
                            'access_status' => 'ACCESS',
                            'validated_access' => 'NO',
                            'verification_code' => $randomNumber,
                            'local_access_date' => Carbon::now()->addMinutes($request->timezoneoffset * -1),
                            'local_timezone_offset' => $request->timezoneoffset
                        ]);

                        echo json_encode([
                            'result' => 1,
                            'message' => 'Validar acceso.',
                            'code' => $randomNumber
                        ]);

                    } else {
                        echo json_encode([
                            'result' => 0,
                            'message' => 'No validar acceso.'
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::error($e);
                    echo json_encode([
                        'result' => 99,
                        'message' => 'Error de comunicacion. Contacte con el administrador.'
                    ]);
                }

            } else {

                echo json_encode([
                    'result' => 2,
                    'message' => 'IP registrada y validada anteriormente.'
                ]);

            }

        } else {
            echo json_encode([
                'result' => 0,
                'message' => 'Datos de acceso erroneos'
            ]);
        }
    }

    public function logTimeout(Request $request)
    {

        $accesos = AccessLog::where('user_email', $request->email)
            ->where('ip_address', $request->ip())
            ->where('access_status', 'ACCESS')
            ->where('validated_access', 'NO')
            ->orderBy('id', 'DESC')
            ->first();

        if (!is_null($accesos)) {
            $accesos->access_status = 'ACCESSTIMEOUT';
            $accesos->save();
        }

    }

}
