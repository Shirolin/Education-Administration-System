<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * @var int 成功状态码
     */
    const SUCCESS = 200;
    /**
     * @var int 失败状态码
     */
    const ERROR = 400;

    /**
     * 返回成功的响应
     *
     * @param mixed|null $data 返回的数据
     * @param string $message 返回的消息
     * @param int $code 返回的状态码
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = null, $message = '操作成功', $code = self::SUCCESS): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 返回失败的响应
     *
     * @param string $message 返回的消息
     * @param mixed|null $data 返回的数据
     * @param int $code 返回的状态码
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($message = '操作失败', $data = null, $code = self::ERROR): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 返回带额外数据的响应
     * （可以添加更多方法，例如处理分页、错误格式化等）
     *
     * @param mixed|null $data
     * @param array $meta
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function withMeta($data = null, $meta = [], $message = '操作成功', $code = self::SUCCESS): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ], $code);
    }
}
