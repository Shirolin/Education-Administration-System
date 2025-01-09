<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 子课程(课程的具体年月份)
 *
 * @property int $id
 * @property int $course_id
 * @property int $year
 * @property int $month
 * @property string $fee
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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

    /**
     * 关联课程
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
