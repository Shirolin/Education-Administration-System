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

        return $query->withCount(['subCourses', 'students'])->paginate($perPage);
    }

    /**
     * 获取单个课程信息
     * @throws Throwable
     */
    public function show(int $id): array
    {
        $course = $this->findCourseOrFail($id);

        return [
            'course' => $course,
            'sub_courses' => $course->subCourses,
        ];
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
