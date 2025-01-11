<?php

namespace App\Services\Student;

use App\Models\Course\Course;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MyCourseService extends BaseService
{
    /**
     * 分页获取课程列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedCourses($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        $query = Course::query();

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "{$filters['name']}%");
        }

        // 只查询当状态正常的课程
        $query->where('status', Course::STATUS_ENABLED);

        // 只查询当前用户的课程
        $query->whereHas('students', function ($query) {
            $query->with('students');
            $query->where('student_id', $this->userId());
        });

        return $query->withCount(['subCourses', 'students'])->paginate($perPage);
    }

    /**
     * 获取单个课程信息
     * @throws Throwable
     */
    public function show(int $id): Course
    {
        $course = $this->findCourseOrFail($id);
        $course->load('subCourses');

        return $course;
    }

    /**
     * 根据ID查找课程，如果找不到则抛出异常
     *
     * @return Course
     * @throws ModelNotFoundException
     */
    public function findCourseOrFail(int $id): Course
    {
        return Course::withCount(['subCourses', 'students'])->findOrFail($id);
    }
}
