<?php

namespace App\Http\Controllers\Teacher\Invoice;

use App\Http\Controllers\ApiController;
use App\Services\Teacher\InvoiceService;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->InvoiceService->index();

        return $this->success($data);
    }

    /**
     * 创建账单
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $data = $this->InvoiceService->store();

        return $this->success($data, '创建成功', 201);
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

    /**
     * 更新账单信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $data = $this->InvoiceService->update($id);

        return $this->success($data, '更新成功');
    }

    /**
     * 删除账单
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->InvoiceService->destroy($id);

        return $this->success($data, '删除成功');
    }

    /**
     * 发送账单
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function send($id)
    {
        $data = $this->InvoiceService->send($id);

        return $this->success($data, '发送成功');
    }
}
