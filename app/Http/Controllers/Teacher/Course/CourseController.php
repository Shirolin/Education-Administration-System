<?php

namespace App\Http\Controllers\Teacher\Course;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Teacher\CourseRequest;
use App\Services\Teacher\CourseService;
use Illuminate\Http\Request;

class CourseController extends ApiController
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
        // $this->middleware('auth:api');
    }

    /**
     * 获取课程列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page');
        $filters = $request->only(['name']);

        $data = $this->courseService->getPaginatedCourses($perPage, $filters);

        return $this->success($data);
    }

    /**
     * 获取单个课程信息
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->courseService->show($id);

        return $this->success($data);
    }

    /**
     * 创建课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CourseRequest $request)
    {
        $course = $request->input('course');
        $subCourses = $request->input('sub_courses');

        $data = $this->courseService->createCourse($course, $subCourses);
        if (!$data) {
            return $this->error('创建失败', $data);
        }

        return $this->success($data, '创建成功');
    }

    /**
     * 更新课程信息
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $courses = [];
        $subCourses = [];

        $data = $this->courseService->updateCourse($id, $courses, $subCourses);
        if (!$data) {
            return $this->error('更新失败', $data);
        }

        return $this->success($data, '更新成功');
    }

    /**
     * 删除课程
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->courseService->deleteCourse($id);
        if (!$data) {
            return $this->error('删除失败', $data);
        }

        return $this->success($data, '删除成功');
    }
}
