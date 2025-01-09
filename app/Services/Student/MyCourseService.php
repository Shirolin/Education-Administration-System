<?php

namespace App\Services\Student;

use App\Models\Course\Course;
use App\Services\BaseService;

class MyCourseService extends BaseService
{
    /**
     * 分页获取课程列表
     * @param int $perPage
     * @param array $filters
     * @return mixed
     */
    public function getPaginatedCourses($perPage = self::DEFAULT_PER_PAGE, $filters = [])
    {
        $query = Course::query();

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "{$filters['name']}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * 获取单个课程信息
     * @param int $id
     * @return array|false
     */
    public function show($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return false;
        }

        return [
            'course' => $course,
            'sub_courses' => $course->subCourses,
        ];
    }
}
