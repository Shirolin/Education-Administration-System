<?php

namespace App\Services\Student;

use App\Models\Course\Course;
use App\Services\BaseService;

class MyCourseService extends BaseService
{
    /**
     * 获取课程列表
     * @return array
     */
    public function index($perPage = 10)
    {
        $data = Course::paginate($perPage);

        return $data;
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
     * 获取单个课程信息
     * @param $id
     * @return array
     */
    public function show($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return [];
        }

        return [
            'course' => $course,
            'sub_courses' => $course->subCourses,
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
