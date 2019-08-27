<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if (!($exception instanceof ValidationException)) {
            $response = [
                'code' => 400,
                'message' => $exception->getMessage(),
            ];

            if ($exception instanceof ModelNotFoundException) {
                $response['code'] = Response::HTTP_NOT_FOUND;
                $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
            } else if ($exception instanceof MethodNotAllowedHttpException) {
                $response['code'] = Response::HTTP_METHOD_NOT_ALLOWED;
                $response['message'] = Response::$statusTexts[Response::HTTP_METHOD_NOT_ALLOWED];
            } else if ($exception instanceof NotFoundHttpException) {
                $response['code'] = Response::HTTP_NOT_FOUND;
                $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
            } else if ($exception instanceof HttpException) {
                $response['code'] = $exception->getStatusCode();
                $response['message'] = Response::$statusTexts[$exception->getCode()];
            }

            // if ($this->isDebugMode()) {
            //     $response['debug'] = [
            //         'exception' => get_class($exception),
            //         'trace' => $exception->getTrace()
            //     ];
            // }

            return response()->json([
                'error' => $response
            ], $response['code']);
        }
        return parent::render($request, $exception);
    }

    public function isDebugMode()
    {
        return (Boolean) env('APP_DEBUG');
    }
}
