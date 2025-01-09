<?php

namespace App\Models\Course;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 课程
 *
 * @property int $id
 * @property int $teacher_id
 * @property string $teacher_nickname
 * @property string $name
 * @property string $unit_fee
 * @property int $sub_courses_count
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSubCoursesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUnitFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course\SubCourse> $subCourses
 * @property-read Teacher|null $teacher
 * @mixin \Eloquent
 */
class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    /**
     * @var int 状态-正常
     */
    const STATUS_ENABLED = 1;
    /**
     * @var int 状态-禁用
     */
    const STATUS_DISABLED = 0;

    /**
     * 关联子课程
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCourses()
    {
        return $this->hasMany(SubCourse::class, 'course_id', 'id');
    }

    /**
     * 关联老师
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    /**
     * 获取状态map
     *
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_ENABLED => '正常',
            self::STATUS_DISABLED => '禁用',
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
     * 是否启用
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->status == self::STATUS_ENABLED;
    }

    /**
     * 是否禁用
     *
     * @return bool
     */
    public function isDisable()
    {
        return $this->status == self::STATUS_DISABLED;
    }
}
