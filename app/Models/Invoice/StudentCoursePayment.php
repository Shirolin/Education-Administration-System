<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
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
}
