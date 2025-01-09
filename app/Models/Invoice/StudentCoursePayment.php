<?php

namespace App\Models\Invoice;

use App\Models\Course\SubCourse;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 学生课程缴费记录
 *
 * @property int $id
 * @property int $student_id
 * @property int $sub_course_id
 * @property int $invoice_item_id
 * @property int $status
 * @property string|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereInvoiceItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereSubCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentCoursePayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StudentCoursePayment extends Model
{
    use HasFactory;

    protected $table = 'student_course_payments';

    /**
     * @var int 状态-待支付
     */
    const STATUS_PENDING = 1;
    /**
     * @var int 状态-已支付
     */
    const STATUS_PAID = 2;

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
     * 关联账单明细项
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id', 'id');
    }

    /**
     * 关联子课程
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id', 'id');
    }

    /**
     * 获取状态map
     *
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_PENDING => '待支付',
            self::STATUS_PAID => '已支付',
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
     * 是否待支付
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
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
}
