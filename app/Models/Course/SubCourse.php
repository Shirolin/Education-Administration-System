<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 子课程(课程的具体年月份)
 *
 * @property int $id 子课程ID
 * @property int $course_id 课程ID
 * @property int $year 年份
 * @property int $month 月份
 * @property string $fee 费用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course\Course|null $course
 * @property-read string $year_month
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCourse whereYear($value)
 * @mixin \Eloquent
 */
class SubCourse extends Model
{
    use HasFactory;

    protected $table = 'sub_courses';

    protected $fillable = [
        'course_id',
        'year',
        'month',
        'fee',
    ];

    protected $appends = [
        'year_month',
    ];

    /**
     * 关联课程
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * 获取子课程的年月份(格式: 202501)
     */
    public function getYearMonthAttribute(): string
    {
        return $this->year . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
}
