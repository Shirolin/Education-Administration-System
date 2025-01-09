<?php

namespace App\Http\Controllers\Student\Invoice;

use App\Http\Controllers\ApiController;
use App\Services\Student\MyInvoiceService;

class MyInvoiceController extends ApiController
{
    protected $InvoiceService;

    public function __construct(MyInvoiceService $InvoiceService)
    {
        $this->InvoiceService = $InvoiceService;
        // $this->middleware('auth:api');
    }

    /**
     * 获取账单列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->InvoiceService->index();

        return $this->success($data);
    }


    /**
     * 获取单个账单信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->InvoiceService->show($id);

        return $this->success($data);
    }
}
