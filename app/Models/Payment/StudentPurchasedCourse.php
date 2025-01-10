<?php

namespace App\Models\Payment;

use App\Models\Course\SubCourse;
use App\Models\Invoice\Invoice;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 学生已购买课程
 *
 * @property int $id 已购买课程ID
 * @property int $invoice_id 账单ID
 * @property int $student_id 学生ID
 * @property int $sub_course_id 子课程ID
 * @property string $purchase_date 购买日期
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereSubCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentPurchasedCourse whereUpdatedAt($value)
 * @property-read Invoice|null $invoice
 * @property-read Student|null $student
 * @property-read SubCourse|null $subCourse
 * @mixin \Eloquent
 */
class StudentPurchasedCourse extends Model
{
    use HasFactory;

    protected $table = 'student_purchased_courses';

    protected $fillable = [
        'invoice_id',
        'student_id',
        'sub_course_id',
        'purchase_date',
    ];

    /**
     * 关联账单
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    /**
     * 关联子课程
     */
    public function subCourse(): BelongsTo
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id', 'id');
    }

    /**
     * 关联学生
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
