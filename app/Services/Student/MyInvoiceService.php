<?php

namespace App\Services\Student;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MyInvoiceService extends BaseService
{
    /**
     * 分页获取账单列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedInvoices($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        $query = Invoice::query();

        if (isset($filters['invoice_no'])) {
            $query->where('invoice_no', 'LIKE', "{$filters['invoice_no']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->withCount(['items'])->paginate($perPage);
    }

    /**
     * 获取单个账单信息
     * @throws Throwable
     */
    public function show(int $id): array
    {
        $invoice = $this->findInvoiceOrFail($id);

        return [
            'invoice' => $invoice,
            'items' => $invoice->items,
        ];
    }

    /**
     * 根据ID查找账单，如果找不到则抛出异常
     *
     * @return Invoice
     * @throws ModelNotFoundException
     */
    public function findInvoiceOrFail(int $id): Invoice
    {
        return Invoice::withCount(['items'])->findOrFail($id);
    }
}
