<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;

class TestController extends ApiController
{
    use ApiResponseTrait;

    public function test()
    {
        return $this->success('Hello World!');
    }
}
