<?php

namespace App\Services\Teacher;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InvoiceService extends BaseService
{
    /**
     * 分页获取账单列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedInvoices($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Invoice::class); // 检查用户是否有权限查看账单列表

        $query = Invoice::query();
        $query->with(['items', 'student', 'course', 'creator']);

        if (isset($filters['invoice_no'])) {
            $query->where('invoice_no', 'LIKE', "{$filters['invoice_no']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->where('creator_id', $this->userId());
        $query->orderBy('id', 'DESC');

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
     * 创建账单及账单项
     *
     * @param array $invoiceData 账单数据
     * - course_id (int) 课程ID
     * - student_id (int) 学生ID
     * - creator_id (int) 创建者ID
     * @param array $itemsData 账单项数据数组，每个元素包含以下字段：
     * - sub_course_id (int) 子课程ID
     * - amount (string) 金额
     * @throws Throwable
     */
    public function createInvoice(array $invoiceData, array $itemsData): bool
    {
        Gate::authorize('create', Invoice::class); // 检查用户是否有权限创建账单

        try {
            DB::transaction(function () use ($invoiceData, $itemsData) {
                $invoice = Invoice::create($invoiceData);
                foreach ($itemsData as $itemData) {
                    $invoice->items()->create($itemData);
                }
            });
        } catch (\Exception $e) {
            Log::error('创建账单失败', [
                'invoice_data' => $invoiceData,
                'items_data' => $itemsData,
                'message' => $e->getMessage(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * 删除账单及所有账单项
     * @throws Throwable
     */
    public function deleteInvoice(int $id): bool
    {
        $invoice = $this->findInvoiceOrFail($id);

        Gate::authorize('delete', $invoice); // 检查用户是否有权限删除账单

        try {
            DB::transaction(function () use ($invoice) {
                $invoice->items()->delete(); // 先删除账单明细项
                $invoice->delete(); // 再删除账单
            });
        } catch (\Exception $e) {
            Log::error('删除账单失败', ['id' => $id, 'message' => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * 取消账单
     * @throws Throwable
     */
    public function cancel(int $id): bool
    {
        $invoice = $this->findInvoiceOrFail($id);

        Gate::authorize('cancel', $invoice); // 检查用户是否有权限取消账单

        try {
            DB::transaction(function () use ($invoice) {
                $invoice->update(['status' => Invoice::STATUS_CANCELLED]);
            });
        } catch (\Exception $e) {
            Log::error('取消账单失败', ['id' => $id, 'message' => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * 发送账单
     * @throws Throwable
     */
    public function send(int $id): bool
    {
        $invoice = $this->findInvoiceOrFail($id);

        Gate::authorize('send', $invoice); // 检查用户是否有权限发送账单

        try {
            DB::transaction(function () use ($invoice) {
                $invoice->update(['status' => Invoice::STATUS_NOTIFIED]);
            });
        } catch (\Exception $e) {
            Log::error('发送账单失败', ['id' => $id, 'message' => $e->getMessage()]);
            return false;
        }

        return true;
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
