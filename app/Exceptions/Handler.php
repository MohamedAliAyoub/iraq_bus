<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
     public function render($request, Throwable $throwable)
    {
        if ($request->wantsJson()) {
            switch ($throwable) {
                case $throwable instanceof ModelNotFoundException || $throwable instanceof NotFoundHttpException:
                    return response()->json([
                        'status' => 'fail',
                        'message' => trans('dashboard.error.page_not_found', [], $request->header('accept-language')),
                        'data' => null
                    ], 404);

                case $throwable instanceof AuthenticationException:

                    return response()->json([
                        'status' => 'fail',
                        'message' => trans('auth.unauth', [], $request->header('accept-language')),
                        'data' => null
                    ], 401);

                case $throwable instanceof ThrottleRequestsException:

                    return response()->json([
                        'status' => 'fail',
                        'message' => trans('auth.throttle', ['seconds' => @$throwable->getHeaders()['Retry-After']], $request->header('accept-language')),
                        'data' => null
                    ], 429);

                case $throwable instanceof MethodNotAllowedHttpException:

                    return response()->json([
                        'status' => 'fail',
                        'message' => trans('dashboard.error.method_not_allow', ['method' => $request->method()], $request->header('accept-language')),
                        'data' => null
                    ], 405);

                case $throwable instanceof \Error || $throwable instanceof \ErrorException || $throwable instanceof \BadMethodCallException:

                    return response()->json([
                        'status' => 'fail',
                        'message' => $throwable->getMessage() . " in " . $throwable->getFile(). " at line " .$throwable->getLine(),
                        'data' => null
                    ], 500);

                    case $throwable instanceof ValidationException:    
                        return response()->json([
                            'status' => 'fail',
                            'message' =>"",
                            'errors' => $throwable->validator->getMessageBag(),
                        ], 422);
            }
        }
        return parent::render($request, $throwable);
    }
}
