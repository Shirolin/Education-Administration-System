<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        if ($request->expectsJson()) {
            if ($exception instanceof ValidationException) {
                return $this->error($exception->validator->errors(), null, 422);
            }
            if ($exception instanceof NotFoundHttpException) {
                return $this->error('资源未找到！', null, 404);
            }
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->error('请求方法不允许！', null, 405);
            }
            if ($exception instanceof AuthenticationException || $exception instanceof OAuthServerException) {
                return $this->error('认证失败！', null, 401);
            }

            return $this->error('服务器错误', null, 500);
        }

        return parent::render($request, $exception);
    }
}
