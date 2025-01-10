<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 退款
 *
 * @property int $id 退款ID
 * @property int $payment_id 支付ID
 * @property string $omise_id Omise ID
 * @property string $amount 金额
 * @property string $currency 货币
 * @property int $status 状态
 * @property string|null $reason 原因
 * @property string|null $refunded_at 退款时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereOmiseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUpdatedAt($value)
 * @property-read string $status_name
 * @property-read \App\Models\Payment\Payment|null $payment
 * @mixin \Eloquent
 */
class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    /**
     * @var int 状态-待退款
     */
    const STATUS_PENDING = 0;

    /**
     * @var int 状态-已退款
     */
    const STATUS_REFUNDED = 1;

    /**
     * @var int 状态-退款失败
     */
    const STATUS_FAILED = 2;

    /**
     * @var int 状态-退款取消
     */
    const STATUS_CANCELLED = 3;

    /**
     * @var int 状态-退款拒绝
     */
    const STATUS_REJECTED = 4;

    /**
     * @var int 状态-退款处理中
     */
    const STATUS_PROCESSING = 5;

    /**
     * @var int 状态-退款成功
     */
    const STATUS_SUCCESS = 6;

    protected $fillable = [
        'payment_id',
        'omise_id',
        'amount',
        'currency',
        'status',
        'reason',
        'refunded_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    protected $appends = ['status_name'];

    /**
     * 关联支付
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    /**
     * 是否已退款
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * 是否退款失败
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * 是否退款取消
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * 是否退款拒绝
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * 是否退款处理中
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * 是否退款成功
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * 获取状态map
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_PENDING => '待退款',
            self::STATUS_REFUNDED => '已退款',
            self::STATUS_FAILED => '退款失败',
            self::STATUS_CANCELLED => '退款取消',
            self::STATUS_REJECTED => '退款拒绝',
            self::STATUS_PROCESSING => '退款处理中',
            self::STATUS_SUCCESS => '退款成功',
        ];
    }

    /**
     * 获取状态名
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }
}
