<?php

namespace App\Services\Teacher;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;

class InvoiceService extends BaseService
{
    /**
     * 分页获取账单列表
     * @param int $perPage
     * @param array $filters
     * @return mixed
     */
    public function getPaginatedInvoices($perPage = self::DEFAULT_PER_PAGE, $filters = [])
    {
        $query = Invoice::query();

        if (isset($filters['invoice_no'])) {
            $query->where('invoice_no', 'LIKE', "{$filters['invoice_no']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    /**
     * 获取单个账单信息
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return false;
        }

        return [
            'invoice' => $invoice,
            'items' => $invoice->items,
        ];
    }

    /**
     * 创建账单
     * @return array
     */
    public function store()
    {
        return [
            'id' => 3,
            'name' => '账单3',
        ];
    }

    /**
     * 更新账单信息
     * @param $id
     * @return array
     */
    public function update($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }

    /**
     * 删除账单
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }

    /**
     * 发送账单
     * @param $id
     * @return array
     */
    public function send($id)
    {
        return [
            'id' => $id,
            'name' => '账单' . $id,
        ];
    }
}
