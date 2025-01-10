<?php

namespace App\Models\Invoice;

use App\Models\Course\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 账单
 *
 * @property int $id
 * @property int $course_id
 * @property int $student_id
 * @property string $total_amount
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @property string $invoice_no
 * @property-read Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice\InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read Student|null $student
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceNo($value)
 * @property int $creator_id
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatorId($value)
 * @property string $currency 货币
 * @property-read Teacher|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCurrency($value)
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
    const STATUS_CANCELLED = 0;

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
    public function getStatusName(): string
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
}
