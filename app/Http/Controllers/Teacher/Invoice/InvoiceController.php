<?php

namespace App\Http\Controllers\Teacher\Invoice;

use App\Http\Controllers\ApiController;
use App\Services\Teacher\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends ApiController
{
    protected $InvoiceService;

    public function __construct(InvoiceService $InvoiceService)
    {
        $this->InvoiceService = $InvoiceService;
        // $this->middleware('auth:api');
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
        if (!$data) {
            return $this->error('账单不存在', $data);
        }

        return $this->success($data);
    }

    /**
     * 创建账单
     */
    public function store(): JsonResponse
    {
        $data = $this->InvoiceService->store();

        return $this->success($data, '创建成功');
    }

    /**
     * 更新账单信息
     */
    public function update(int $id): JsonResponse
    {
        $data = $this->InvoiceService->update($id);

        return $this->success($data, '更新成功');
    }

    /**
     * 删除账单
     */
    public function destroy(int $id): JsonResponse
    {
        $data = $this->InvoiceService->destroy($id);

        return $this->success($data, '删除成功');
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
