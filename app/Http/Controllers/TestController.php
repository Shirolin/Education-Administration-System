<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends ApiController
{

    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function test()
    {
        $user = User::whereRole(1)->first();

        // if (!$user) {
        //     return $this->error('用户不存在');
        // }

        return $this->success($user);
    }
}
