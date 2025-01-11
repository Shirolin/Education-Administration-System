<?php

namespace App\Http\Controllers\Student\Invoice;

use App\Http\Controllers\ApiController;
use App\Services\Student\MyInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyInvoiceController extends ApiController
{
    protected $InvoiceService;

    public function __construct(MyInvoiceService $InvoiceService)
    {
        $this->InvoiceService = $InvoiceService;
    }

    /**
     * 获取账单列表
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page');
        $filters = $request->only(['invoice_no', 'status']);

        $data = $this->InvoiceService->getPaginatedInvoices($perPage, $filters);

        return $this->success($data);
    }

    /**
     * 获取单个账单信息
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->InvoiceService->show($id);

        return $this->success($data);
    }

    /**
     * 获取待支付账单数量
     */
    public function unpaidCount(): JsonResponse
    {
        $data = $this->InvoiceService->unpaidCount();

        return $this->success($data);
    }

    /**
     * 支付账单
     */
    public function pay(int $id): JsonResponse
    {
        $data = $this->InvoiceService->pay($id);

        return $this->success($data, '支付成功');
    }
}
