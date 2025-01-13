<?php

namespace App\Services\Student;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use App\Services\Payment\OmisePaymentService;

class MyInvoiceService extends BaseService
{
    protected $paymentService;

    public function __construct(OmisePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * 分页获取账单列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedInvoices($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Invoice::class); // 检查用户是否有权限查看账单列表

        $query = Invoice::query();
        $query->with(['items']);

        if (isset($filters['invoice_no'])) {
            $query->where('invoice_no', 'LIKE', "{$filters['invoice_no']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 只查询当前用户的账单
        $query->where('student_id', $this->userId());

        // 只显示已通知、已支付、已取消的账单
        $query->whereIn('status', [
            Invoice::STATUS_NOTIFIED,
            Invoice::STATUS_PAID,
            Invoice::STATUS_CANCELLED,
        ]);

        // 把已通知的账单排在前面
        $query->orderBy('status', 'ASC')->orderBy('id', 'DESC');

        return $query->withCount(['items'])->paginate($perPage);
    }

    /**
     * 获取单个账单信息
     * @throws Throwable
     */
    public function show(int $id): Invoice
    {
        $invoice = $this->findInvoiceOrFail($id);
        $invoice->load(['items', 'items.subCourse']);

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
     * 支付账单
     */
    public function pay(int $id, string $omiseToken = ''): bool
    {
        $invoice = $this->findInvoiceOrFail($id);
        Gate::authorize('pay', $invoice); // 检查用户是否有权限支付账单

        try {
            // 调用 Omise 支付服务
            return $this->paymentService->processPayment($invoice, $this->user(), $omiseToken);
        } catch (\Exception $e) {
            throw new \Exception('支付失败: ' . $e->getMessage());
        }
    }

    /**
     * 根据ID查找账单，如果找不到则抛出异常
     *
     * @return Invoice
     * @throws ModelNotFoundException
     */
    public function findInvoiceOrFail(int $id): Invoice
    {
        $invoice = Invoice::withCount(['items'])->findOrFail($id);

        Gate::authorize('view', $invoice); // 检查用户是否有权限查看账单

        return $invoice;
    }
}
