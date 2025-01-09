<?php

namespace App\Services\Student;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;

class MyInvoiceService extends BaseService
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
     * @return array|false
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
}
