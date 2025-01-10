<?php

namespace App\Models\Course;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 课程
 *
 * @property int $id
 * @property int $teacher_id
 * @property string $teacher_nickname
 * @property string $name
 * @property string $unit_fee
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
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUnitFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course\SubCourse> $subCourses
 * @property-read Teacher|null $teacher
 * @property-read int|null $students_count
 * @property-read int|null $sub_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read string $status_name
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

    protected $attributes = [
        'status' => self::STATUS_ENABLED,
    ];

    protected $fillable = [
        'teacher_id',
        'teacher_nickname',
        'name',
        'unit_fee',
        'status',
    ];

    protected $appends = ['status_name'];

    /**
     * 关联子课程
     */
    public function subCourses(): HasMany
    {
        return $this->hasMany(SubCourse::class, 'course_id', 'id');
    }

    /**
     * 关联老师
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    /**
     * 关联学生
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id');
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
    public function getStatusNameAttribute()
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

    /**
     * 获取子课程数量
     *
     * @return int
     */
    public function getSubCoursesCountAttribute()
    {
        if ($this->relationLoaded('subCourses')) {
            return $this->subCourses->count();
        }

        return $this->subCourses()->count();
    }

    /**
     * 获取关联学生数量
     *
     * @return int
     */
    public function getStudentsCountAttribute()
    {
        if ($this->relationLoaded('students')) {
            return $this->students->count();
        }

        return $this->students()->count();
    }
}
