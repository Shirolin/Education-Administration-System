<?php

namespace App\Models\Invoice;

use App\Models\Course\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    /**
     * 关联学生
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * 关联课程
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * 获取状态map
     *
     * @return array
     */
    public static function getStatusMap()
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
     *
     * @return string
     */
    public function getStatusName()
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }

    /**
     * 是否待通知
     *
     * @return bool
     */
    public function isPendingNotify()
    {
        return $this->status == self::STATUS_PENDING_NOTIFY;
    }

    /**
     * 是否已通知
     *
     * @return bool
     */
    public function isNotified()
    {
        return $this->status == self::STATUS_NOTIFIED;
    }

    /**
     * 是否已支付
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    /**
     * 是否已取消
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }
}
