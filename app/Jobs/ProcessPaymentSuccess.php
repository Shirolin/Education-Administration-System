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
     * 支付数据示例
     */
    protected $exampleChargeData = [
        'object' => 'charge',
        'id' => 'chrg_test_62djldeepxr0q3tpwka',
        'location' => '/charges/chrg_test_62djldeepxr0q3tpwka',
        'amount' => 10000,
        'acquirer_reference_number' => null,
        'net' => 9675,
        'fee' => 295,
        'fee_vat' => 30,
        'interest' => 0,
        'interest_vat' => 0,
        'funding_amount' => 10000,
        'refunded_amount' => 0,
        'transaction_fees' => [
            'fee_flat' => '0.0',
            'fee_rate' => '2.95',
            'vat_rate' => '10.0'
        ],
        'platform_fee' => [
            'fixed' => null,
            'amount' => null,
            'percentage' => null
        ],
        'currency' => 'JPY',
        'funding_currency' => 'JPY',
        'ip' => null,
        'refunds' => [
            'object' => 'list',
            'data' => [],
            'limit' => 20,
            'offset' => 0,
            'total' => 0,
            'location' => '/charges/chrg_test_62djldeepxr0q3tpwka/refunds',
            'order' => 'chronological',
            'from' => '1970-01-01T00:00:00Z',
            'to' => '2025-01-11T11:01:15Z'
        ],
        'link' => null,
        'description' => null,
        'metadata' => [],
        'card' => [
            'object' => 'card',
            'id' => 'card_test_62djlc295lpb37ad2kq',
            'livemode' => false,
            'location' => null,
            'deleted' => false,
            'street1' => null,
            'street2' => null,
            'city' => null,
            'state' => null,
            'phone_number' => null,
            'postal_code' => null,
            'country' => 'us',
            'financing' => 'credit',
            'bank' => 'JPMORGAN CHASE BANK N.A.',
            'brand' => 'Visa',
            'fingerprint' => 'Vo+guhDrI7CHDTfeFz4wlt7qerOgZUT6IGZGgx6enxU=',
            'first_digits' => null,
            'last_digits' => '0011',
            'name' => 'Shirolin',
            'expiration_month' => 1,
            'expiration_year' => 2025,
            'security_code_check' => true,
            'tokenization_method' => null,
            'created_at' => '2025-01-11T11:01:09Z'
        ],
        'source' => null,
        'schedule' => null,
        'linked_account' => null,
        'customer' => null,
        'dispute' => null,
        'transaction' => null,
        'failure_code' => 'insufficient_fund',
        'failure_message' => 'insufficient funds in the account or the card has reached the credit limit',
        'merchant_advice' => null,
        'status' => 'failed',
        'authorize_uri' => null,
        'return_uri' => null,
        'created_at' => '2025-01-11T11:01:15Z',
        'paid_at' => null,
        'authorized_at' => null,
        'expires_at' => '2025-02-10T11:01:15Z',
        'expired_at' => null,
        'reversed_at' => null,
        'multi_capture' => false,
        'zero_interest_installments' => false,
        'branch' => null,
        'terminal' => null,
        'device' => null,
        'authorized' => false,
        'capturable' => false,
        'capture' => true,
        'disputable' => false,
        'livemode' => false,
        'refundable' => false,
        'partially_refundable' => false,
        'reversed' => false,
        'reversible' => false,
        'voided' => false,
        'paid' => false,
        'expired' => false,
        'can_perform_void' => false,
        'approval_code' => null
    ];

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
                // 查出账单对应的课程的子课程
                $subCourses = $invoice->course->subCourses;
                $studentId = $invoice->student_id;
                foreach ($subCourses as $subCourse) {
                    StudentPurchasedCourse::create([
                        'invoice_id'    => $this->invoiceId,
                        'student_id'    => $studentId,
                        'sub_course_id' => $subCourse->id,
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
