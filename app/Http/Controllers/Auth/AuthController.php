<?php

namespace App\Http\Controllers\Auth;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends PassportAccessTokenController
{
    use ApiResponseTrait;

    /**
     * 登录 (重写Passport 的 issueToken 方法)
     */
    public function login(ServerRequestInterface $request): JsonResponse
    {
        try {
            $response = parent::issueToken($request); // 调用父类方法获取原始响应
        } catch (\Exception $e) {
            return $this->error('用户名或密码错误', ['error' => $e->getMessage()]);
        }

        $content = json_decode($response->getContent(), true);

        if (isset($content['error'])) {
            return $this->error('用户名或密码错误', $content);
        }

        return $this->success($content);
    }

    /**
     * 退出登录
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return $this->success(['message' => '登出成功']);
    }

    /**
     * 获取用户信息
     */
    public function user(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }
}
