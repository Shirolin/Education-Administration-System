<?php

namespace App\Http\Controllers\Auth;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends PassportAccessTokenController
{
    use ApiResponseTrait;

    /**
     * 登录
     * (重写Passport 的 issueToken 方法)
     *
     * @param ServerRequestInterface $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(ServerRequestInterface $request)
    {
        $response = parent::issueToken($request); // 调用父类方法获取原始响应

        $content = json_decode($response->getContent(), true);

        if (isset($content['error'])) {
            return $this->error('用户名或密码错误', $content);
        }

        return $this->success($content);
    }

    /**
     * 退出登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success(['message' => '登出成功']);
    }
}
