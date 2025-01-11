<?php

namespace App\Http\Controllers\Student\Course;

use App\Http\Controllers\ApiController;
use App\Services\Student\MyCourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyCourseController extends ApiController
{
    protected $courseService;

    public function __construct(MyCourseService $courseService)
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
}
