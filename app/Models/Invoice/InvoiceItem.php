<?php

namespace App\Models\Invoice;

use App\Models\Course\SubCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 账单明细项
 *
 * @property int $id 账单明细ID
 * @property int $invoice_id 账单ID
 * @property int $sub_course_id 子课程ID
 * @property string $amount 金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice\Invoice|null $invoice
 * @property-read SubCourse|null $subCourse
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereSubCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

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
}
