<?php

namespace App\Services\Teacher;

use App\Models\Invoice\Invoice;
use App\Services\BaseService;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $query = Invoice::query();
        $query->with(['items', 'student', 'course']);

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
     * 创建账单及账单项
     *
     * @param array $invoiceData 账单数据
     * @param array $itemsData 账单项数据数组
     * @throws Throwable
     */
    public function createInvoice(array $invoiceData, array $itemsData): bool
    {
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
     * 更新账单及账单项
     *
     * @param int $id 账单ID
     * @param array $invoiceData 账单数据
     * @param array $itemsData 账单项数据数组(全量)
     * @throws Throwable
     */
    public function update(int $id, array $invoiceData, array $itemsData): bool
    {
        $invoice = Invoice::findOrFail($id);

        try {
            DB::transaction(function () use ($invoice, $invoiceData, $itemsData) {
                $invoice->update($invoiceData);

                // 更新账单项(新增、更新、删除)
                $itemIds = array_column($itemsData, 'id');
                $invoice->items()->whereNotIn('id', $itemIds)->delete();

                foreach ($itemsData as $itemData) {
                    if (isset($itemData['id'])) {
                        $invoice->items()->where('id', $itemData['id'])->update($itemData);
                    } else {
                        $invoice->items()->create($itemData);
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('更新账单失败', [
                'id' => $id,
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
     * 发送账单
     * @throws Throwable
     */
    public function send(int $id): bool
    {
        $invoice = $this->findInvoiceOrFail($id);

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
        return Invoice::withCount(['items'])->findOrFail($id);
    }
}
