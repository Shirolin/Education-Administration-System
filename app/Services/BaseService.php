<?php

namespace App\Services;

class BaseService
{
    /**
     * 默认每页显示数量
     */
    const DEFAULT_PER_PAGE = 10;

    /**
     * 获取当前用户
     */
    protected function user()
    {
        return auth('api')->user();
    }

    /**
     * 获取当前用户ID
     */
    protected function userId()
    {
        return auth('api')->id();
    }
}
