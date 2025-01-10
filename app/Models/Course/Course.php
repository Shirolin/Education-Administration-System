<?php

namespace App\Models\Course;

use App\Models\Role\Student;
use App\Models\Role\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 课程
 *
 * @property int $id 课程ID
 * @property int $teacher_id 教师ID
 * @property string $teacher_nickname 教师昵称
 * @property string $name 课程名称
 * @property string $unit_fee 单价
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status_name
 * @property-read int|null $students_count
 * @property-read int|null $sub_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course\SubCourse> $subCourses
 * @property-read Teacher|null $teacher
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
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_ENABLED => '正常',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    /**
     * 获取状态名
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }

    /**
     * 是否启用
     */
    public function isEnable(): bool
    {
        return $this->status == self::STATUS_ENABLED;
    }

    /**
     * 是否禁用
     */
    public function isDisable(): bool
    {
        return $this->status == self::STATUS_DISABLED;
    }

    /**
     * 获取子课程数量
     */
    public function getSubCoursesCountAttribute(): int
    {
        if ($this->relationLoaded('subCourses')) {
            return $this->subCourses->count();
        }

        return $this->subCourses()->count();
    }

    /**
     * 获取关联学生数量
     */
    public function getStudentsCountAttribute(): int
    {
        if ($this->relationLoaded('students')) {
            return $this->students->count();
        }

        return $this->students()->count();
    }

    /**
     * 检查传入的用户ID是否是该课程的老师
     */
    public function isTeacher(int $teacherId): bool
    {
        return $this->teacher_id === $teacherId;
    }

    /**
     * 检查传入的用户ID是否是该课程的学生
     */
    public function isStudent(int $studentId): bool
    {
        return $this->students()->where('student_id', $studentId)->exists();
    }
}
