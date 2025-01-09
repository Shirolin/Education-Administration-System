<?php

namespace App\Services\Teacher;

use App\Models\Course\Course;
use App\Services\BaseService;

class CourseService extends BaseService
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
     * @return array
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

    /**
     * 创建课程
     * @return array
     */
    public function store()
    {
        return [
            'id' => 3,
            'name' => '课程3',
        ];
    }

    /**
     * 更新课程信息
     * @param $id
     * @return array
     */
    public function update($id)
    {
        return [
            'id' => $id,
            'name' => '课程' . $id,
        ];
    }

    /**
     * 删除课程
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        return [
            'id' => $id,
            'name' => '课程' . $id,
        ];
    }
}
