<?php

namespace App\Jobs;

use App\Models\Invoice\Invoice;
use App\Models\Payment\Payment;
use App\Models\Payment\StudentPurchasedCourse;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentSuccess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;  // 账单 ID
    protected $omiseToken; // Omise Token
    protected $chargeData; // 支付数据


    public $tries = 3; // 重试次数

    /**
     * 创建付款成功处理任务
     */
    public function __construct(int $invoiceId, string $omiseToken, array $chargeData)
    {
        $this->invoiceId = $invoiceId;
        $this->omiseToken = $omiseToken;
        $this->chargeData = $chargeData;
    }

    /**
     * 执行付款成功处理任务
     */
    public function handle(): void
    {
        Log::info('异步任务处理支付成功逻辑开始', ['invoiceId' => $this->invoiceId, 'charge' => $this->chargeData]);
        try {
            DB::transaction(function () {
                // 创建 payments 记录
                $amount = isset($this->chargeData['amount']) ? $this->chargeData['amount'] / 100 : 0;
                Payment::create([
                    'invoice_id'      => $this->invoiceId,
                    'omise_id'        => $this->omiseToken,
                    'amount'          => $amount,
                    'currency'        => $this->chargeData['currency'],
                    'card_id'         => $this->chargeData['card']['id'],
                    'charge_id'       => $this->chargeData['id'],
                    'failure_code'    => $this->chargeData['failure_code'],
                    'failure_message' => $this->chargeData['failure_message'],
                    'authorized'      => $this->chargeData['authorized'],
                    'paid'            => $this->chargeData['status'] === 'successful',
                    'transaction_id'  => $this->chargeData['id'],
                    'status'          => $this->chargeData['status'] === 'successful' ? Payment::STATUS_PAID : Payment::STATUS_FAILED,
                ]);

                // 更新账单状态
                $invoice = Invoice::find($this->invoiceId);
                $invoice->status = Invoice::STATUS_PAID;
                $invoice->save();

                // 如果支付成功，更新学生已购买子课程
                // 查出账单明细对应的子课程，然后创建学生已购买子课程记录
                $invoiceItems = $invoice->items;
                foreach ($invoiceItems as $invoiceItem) {
                    StudentPurchasedCourse::create([
                        'invoice_id'    => $this->invoiceId,
                        'student_id'    => $invoice->student_id,
                        'sub_course_id' => $invoiceItem->sub_course_id,
                        'purchase_date' => Carbon::now(),
                    ]);
                }
            });
            Log::info('异步任务处理支付成功逻辑完成', ['invoiceId' => $this->invoiceId, 'charge' => $this->chargeData]);
        } catch (\Exception $e) {
            Log::error('处理支付成功逻辑失败', ['invoiceId' => $this->invoiceId, 'charge' => $this->chargeData, 'message' => $e->getMessage()]);
        }

    }
}
