<?php

namespace App\Http\Controllers\Teacher\Course;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Teacher\Course\CreateCourseRequest;
use App\Http\Requests\Teacher\Course\UpdateCourseRequest;
use App\Services\Teacher\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends ApiController
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * 获取课程列表
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page');
        $filters = $request->only(['name']);

        $data = $this->courseService->getPaginatedCourses($perPage, $filters);

        return $this->success($data);
    }

    /**
     * 获取单个课程信息
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->courseService->show($id);

        return $this->success($data);
    }

    /**
     * 创建课程
     */
    public function store(CreateCourseRequest $request): JsonResponse
    {
        $course = $request->input('course');
        $subCourses = $request->input('sub_courses');
        $studentIds = $request->input('student_ids');

        $data = $this->courseService->createCourse($course, $subCourses, $studentIds);
        if (!$data) {
            return $this->error('创建失败', $data);
        }

        return $this->success($data, '创建成功');
    }

    /**
     * 更新课程信息
     */
    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        $course = $request->input('course');
        $subCourses = $request->input('sub_courses');
        $studentIds = $request->input('student_ids');

        $data = $this->courseService->updateCourse($id, $course, $subCourses, $studentIds);
        if (!$data) {
            return $this->error('更新失败', $data);
        }

        return $this->success($data, '更新成功');
    }

    /**
     * 删除课程
     */
    public function destroy(int $id): JsonResponse
    {
        $data = $this->courseService->deleteCourse($id);
        if (!$data) {
            return $this->error('删除失败', $data);
        }

        return $this->success($data, '删除成功');
    }
}
