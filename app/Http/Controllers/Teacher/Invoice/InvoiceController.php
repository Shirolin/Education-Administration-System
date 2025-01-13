<?php

namespace App\Http\Controllers\Teacher\Invoice;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Teacher\Invoice\CreateInvoiceRequest;
use App\Services\Teacher\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends ApiController
{
    protected $InvoiceService;

    public function __construct(InvoiceService $InvoiceService)
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
     * 创建账单
     */
    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $invoice = $request->getInvoiceData();
        $invoiceItems = $request->getInvoiceItemsData();

        $data = $this->InvoiceService->createInvoice($invoice, $invoiceItems);
        if (!$data) {
            return $this->error('创建失败', $data);
        }

        return $this->success($data, '创建成功');
    }

    /**
     * 删除账单
     */
    public function destroy(int $id): JsonResponse
    {
        $data = $this->InvoiceService->deleteInvoice($id);

        return $this->success($data, '删除成功');
    }

    /**
     * 取消账单
     */
    public function cancel(int $id): JsonResponse
    {
        $data = $this->InvoiceService->cancel($id);

        return $this->success($data, '取消成功');
    }

    /**
     * 发送账单
     */
    public function send(int $id): JsonResponse
    {
        $data = $this->InvoiceService->send($id);

        return $this->success($data, '发送成功');
    }
}
