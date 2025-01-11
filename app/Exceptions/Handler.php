<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        $errMsg = $exception->getMessage();

        if ($request->expectsJson()) {
            if ($exception instanceof ValidationException) {
                return $this->error($exception->validator->errors(), $errMsg, SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
                return $this->error('资源未找到！', null, SymfonyResponse::HTTP_NOT_FOUND);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->error('请求方法不允许！', null, SymfonyResponse::HTTP_METHOD_NOT_ALLOWED);
            }
            if ($exception instanceof AuthenticationException || $exception instanceof OAuthServerException) {
                return $this->error('认证失败！', null, SymfonyResponse::HTTP_UNAUTHORIZED);
            }
            if ($exception instanceof AuthorizationException) {
                return $this->error($errMsg, null, SymfonyResponse::HTTP_FORBIDDEN);
            }

            return $this->error('服务器错误', $errMsg, SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
