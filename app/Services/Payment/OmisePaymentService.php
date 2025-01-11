<?php

namespace App\Services\Payment;

use App\Models\Invoice\Invoice;
use App\Models\Role\User;
use Illuminate\Support\Facades\Log;
use OmiseCharge;

class OmisePaymentService
{
    protected $publickey;  // 用于存储 Omise 的公钥
    protected $secretkey;  // 用于存储 Omise 的私钥
    protected $omiseToken; // 用于存储 Omise 的 token，由前端生成
    protected $source;     // 来源
    protected $customer;   // 消费者

    /**
     * @var string 默认货币
     */
    const DEFAULT_CURRENCY = 'CNY';

    /**
     * @var int 来源-课程
     */
    const SOURCE_COURSE = 1;

    public function __construct()
    {
        $this->publickey = env('OMISE_PUBLIC_KEY');
        $this->secretkey = env('OMISE_SECRET_KEY');
    }

    /**
     * 处理 Omise 支付
     *
     * @param Invoice $invoice
     * @param User $user
     * @param string $omiseToken
     * @return bool
     * @throws \Exception
     */
    public function processPayment(Invoice $invoice, User $user, $omiseToken = ''): bool
    {
        if ($invoice->total_amount <= 0 || empty($omiseToken)) {
            throw new \Exception('支付金额或 Omise Token 无效');
        }

        $this->omiseToken = $omiseToken;
        $this->source = self::SOURCE_COURSE;
        $this->customer = $user->id;
        $description = 'Invoice Payment: ' . $invoice->invoice_no;

        return $this->createCharge($invoice->total_amount, $invoice->currency ?? self::DEFAULT_CURRENCY, $description);
    }

    /**
     * 创建 Omise 支付
     *
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @return bool
     * @throws \Exception
     */
    protected function createCharge($amount, $currency, $description): bool
    {
        try {
            $charge = OmiseCharge::create([
                'amount'      => $amount * 100,       // Omise 以最小货币单位（如分）为单位
                'currency'    => $currency,
                'description' => $description,
                'card'        => $this->omiseToken,
                'source'      => $this->source,
            ]);

            if ($charge['status'] === 'successful') {
                return true;
            }
        } catch (\Exception $e) {
            // 处理支付异常
            Log::error('支付失败', ['code' => $e->getCode(), 'message' => $e->getMessage()]);
            throw new \Exception($e->getMessage());
        }

        return false;
    }
}
