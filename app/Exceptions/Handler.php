<?php namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;
use Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return RedirectResponse|void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Exception $exception
     * @return Factory|Application|JsonResponse|RedirectResponse|Response|View
     */
    public function render($request, Throwable $exception)
    {
        /*//LeoW manejar de forma distitnta cuando son request ajax
        $ajax_response = $this->checkHttpAjax($request,$exception);
        if(isset($ajax_response)) return $ajax_response; 

        $exceptionCode = $exception->getCode();
	//return parent::render($request, $exception);
        if ( $exception instanceof PDOException && $exceptionCode != "42S02" )
        {
            $errorMessage = "Ha ocurrido un error de base de datos, por favor verifique el log de la aplicación y las configuraciones de cada una de las bases de datos.";

            Session::flash('message-error', $errorMessage );

            return redirect()
                ->back()
                ->withInput();
        }

        if ($exception instanceof AuthorizationException) {

            if ( $request->ajax() == true )
            {
                return response()->json([
                    'success' => 'false',
                    'message' => $exception->getMessage()
                ], 401 );
            }

            $errorMessage = "Ha ocurrido una excepción con su autenticación, por favor, intente más tarde.";

            Session::flash('message-error', $errorMessage );

            return redirect()
                ->guest('login');
        }

        if (
            $exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException
        )
        {

            if ( $request->ajax() == true )
            {
                return response()->json([
                    'success' => 'false',
                    'message' => $exception->getMessage()
                ], 401 );
            }

            $errorMessage = "El recurso que busca no ha sido encontrado, por favor verifique.";

            Session::flash('message-error', $errorMessage );

            return redirect()
                ->guest('login');
        }

        /*
         * Notification when the user try to load a file too large
         * **/
        /*if ($exception instanceof PostTooLargeException)
            return response( "Archivo demasiado grande", 422);

        if ( $exception instanceof TokenMismatchException )
        {
            $errorMessage = "Token inválido, por favor verifique!!";

            if ( $request->ajax() )
            {
                return response([
                    'success'   => false,
                    'message'   => $errorMessage,
                ], 422 );
            }

            Session::flash('message-error', $errorMessage );

            return redirect()
                ->guest('login' );
        }

        if ( $exception->getMessage() == "Unauthenticated." )
        {
            $errorMessage = "Por favor inicie sesión nuevamente en la aplicación.";

            Session::flash('message-error', $errorMessage );

            return redirect()
                ->guest('login' );
        }

        $errorMessage = "Ha ocurrido un error inesperado, por favor comuníquese con el administrador del sistema.";

        Session::flash('message-error', $errorMessage );

        //return redirect()->back();
*/
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request  $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    private function checkHttpAjax(Request $request,Exception $exception)
    {
        if($request->ajax() && $request->hasHeader('http-react'))
        {
            switch ($exception->getMessage()) {
                case 'Unauthenticated.':
                    Session::flash('message-error', 'Por favor inicie sesión nuevamente en la aplicación.' );
        
                    return response($exception->getMessage(),399);

                    break;
                
                default:
                    return response($exception->getMessage(),500);
                    break;
            }
            
        }

        return null;
    }
}
