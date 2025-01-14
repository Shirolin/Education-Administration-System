<?php

namespace App\Models\Invoice;

use App\Models\Course\Course;
use App\Models\Role\Student;
use App\Models\Role\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 账单
 *
 * @property int $id 账单ID
 * @property string $invoice_no 账单编号
 * @property int $course_id 课程ID
 * @property int $student_id 学生ID
 * @property int $creator_id 创建者ID
 * @property string $total_amount 总金额
 * @property string $currency 货币
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Course|null $course
 * @property-read Teacher|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    /**
     * @var int 状态-待通知
     */
    const STATUS_PENDING_NOTIFY = 1;
    /**
     * @var int 状态-已通知
     */
    const STATUS_NOTIFIED = 2;
    /**
     * @var int 状态-已支付
     */
    const STATUS_PAID = 3;
    /**
     * @var int 状态-已取消
     */
    const STATUS_CANCELLED = 4;

    protected $attributes = [
        'status' => self::STATUS_PENDING_NOTIFY,
        'currency' => 'JPY',
    ];

    protected $fillable = [
        'invoice_no',
        'course_id',
        'student_id',
        'creator_id',
        'total_amount',
        'currency',
        'status',
    ];

    protected $appends = [
        'status_name',
        'creator_name',
        'student_name',
        'course_name',
        'operation_status',
    ];

    /**
     * 关联账单明细项
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    /**
     * 关联学生
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * 关联课程
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * 关联创建者(教师)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'creator_id', 'id');
    }

    /**
     * 获取状态map
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_PENDING_NOTIFY => '待通知',
            self::STATUS_NOTIFIED => '已通知',
            self::STATUS_PAID => '已支付',
            self::STATUS_CANCELLED => '已取消',
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
     * 是否待通知
     */
    public function isPendingNotify(): bool
    {
        return $this->status == self::STATUS_PENDING_NOTIFY;
    }

    /**
     * 是否已通知
     */
    public function isNotified(): bool
    {
        return $this->status == self::STATUS_NOTIFIED;
    }

    /**
     * 是否已支付
     */
    public function isPaid(): bool
    {
        return $this->status == self::STATUS_PAID;
    }

    /**
     * 是否已取消
     */
    public function isCancelled(): bool
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    /**
     * 是否可以发送账单
     */
    public function canSend(): bool
    {
        return $this->isPendingNotify();
    }

    /**
     * 是否可以支付账单
     */
    public function canPay(): bool
    {
        return $this->isNotified();
    }

    /**
     * 是否可以取消账单
     */
    public function canCancel(): bool
    {
        return $this->isPendingNotify() || $this->isNotified();
    }

    /**
     * 检查传入的用户ID是否是账单的创建者
     */
    public function isCreator(int $teacherId): bool
    {
        return $this->creator_id == $teacherId;
    }

    /**
     * 检查传入的用户ID是否是账单的学生
     */
    public function isStudent(int $studentId): bool
    {
        return $this->student_id == $studentId;
    }

    /**
     * 获取创建者名称
     */
    public function getCreatorNameAttribute(): string
    {
        return $this->creator->nickname ?? '未知';
    }

    /**
     * 获取学生名称
     */
    public function getStudentNameAttribute(): string
    {
        return $this->student->nickname ?? '未知';
    }

    /**
     * 获取课程名称
     */
    public function getCourseNameAttribute(): string
    {
        return $this->course->name ?? '未知';
    }

    /**
     * 获取账单操作状态集合
     */
    public function getOperationStatusAttribute(): array
    {
        return [
            'canPay' => $this->canPay(),
            'canCancel' => $this->canCancel(),
            'canSend' => $this->canSend(),
        ];
    }

    /**
     * 生成唯一的账单编号
     */
    public static function generateInvoiceNo(): string
    {
        do {
            $invoiceNo = 'INV' . substr(uniqid(), -17);
        } while (self::where('invoice_no', $invoiceNo)->exists());

        return $invoiceNo;
    }
}
