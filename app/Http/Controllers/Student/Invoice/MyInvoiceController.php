<?php

namespace App\Http\Controllers\Student\Invoice;

use App\Http\Controllers\ApiController;
use App\Services\Student\MyInvoiceService;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page');
        $filters = $request->only(['invoice_no', 'status']);

        $data = $this->InvoiceService->getPaginatedInvoices($perPage, $filters);

        return $this->success($data);
    }

    /**
     * 获取单个账单信息
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->InvoiceService->show($id);
        if (!$data) {
            return $this->error('账单不存在', $data);
        }

        return $this->success($data);
    }
}
