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

        // 只查询当前用户的账单
        $query->whereHas('students', function ($query) {
            $query->with('students');
            $query->where('student_id', $this->userId());
        });

        // 只显示已通知、已支付、已取消的账单
        $query->whereIn('status', [
            Invoice::STATUS_NOTIFIED,
            Invoice::STATUS_PAID,
            Invoice::STATUS_CANCELLED,
        ]);

        // 把已通知的账单排在前面
        $query->orderBy('status', 'asc');

        return $query->withCount(['items'])->paginate($perPage);
    }

    /**
     * 获取单个账单信息
     * @throws Throwable
     */
    public function show(int $id): Invoice
    {
        $invoice = $this->findInvoiceOrFail($id);
        $invoice->load('items');

        return $invoice;
    }

    /**
     * 获取待支付账单数量(已通知)
     */
    public function unpaidCount(): int
    {
        return Invoice::where('status', Invoice::STATUS_NOTIFIED)->count();
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
