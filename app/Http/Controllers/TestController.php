<?php

namespace App\Http\Controllers;

use App\Models\Role\User;
use Illuminate\Http\Request;

class TestController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function test(Request $request)
    {
        $user = $request->user();
        return $this->success($user);
    }
}
