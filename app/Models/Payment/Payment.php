<?php

namespace App\Models\Payment;

use App\Models\Invoice\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 支付
 *
 * @property int $id 支付ID
 * @property int $invoice_id 账单ID
 * @property string $omise_id Omise ID
 * @property string $amount 金额
 * @property string $currency 货币
 * @property string $card_id 卡ID
 * @property string $charge_id 收费ID
 * @property string|null $failure_code 失败代码
 * @property string|null $failure_message 失败信息
 * @property bool $authorized 是否授权
 * @property bool $paid 是否支付
 * @property string|null $transaction_id 交易ID
 * @property int $status 状态
 * @property string|null $paid_at 支付时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAuthorized($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereChargeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailureCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereFailureMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOmiseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @property-read string $status_name
 * @property-read Invoice|null $invoice
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    /**
     * @var int 状态-待支付
     */
    const STATUS_PENDING = 1;
    /**
     * @var int 状态-已支付
     */
    const STATUS_PAID = 2;
    /**
     * @var int 状态-失败
     */
    const STATUS_FAILED = 3;
    /**
     * @var int 状态-已取消
     */
    const STATUS_CANCELLED = 0;

    protected $fillable = [
        'invoice_id',
        'omise_id',
        'amount',
        'currency',
        'card_id',
        'charge_id',
        'failure_code',
        'failure_message',
        'authorized',
        'paid',
        'transaction_id',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'authorized' => 'boolean',
        'paid' => 'boolean',
    ];

    protected $appends = ['status_name'];

    /**
     * 关联账单
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    /**
     * 是否已取消
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * 是否待支付
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * 是否已支付
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * 是否支付失败
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * 获取状态map
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_PENDING => '待支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_FAILED => '失败',
        ];
    }

    /**
     * 获取状态名
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }

    /**
     * 是否已操作支付
     */
    public function isOperatedPaid(): bool
    {
        return $this->paid;
    }

    /**
     * 是否已授权
     */
    public function isAuthorized(): bool
    {
        return $this->authorized;
    }
}
