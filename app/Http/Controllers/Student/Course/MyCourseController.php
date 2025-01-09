<?php

namespace App\Http\Controllers\Student\Course;

use App\Http\Controllers\ApiController;
use App\Services\Student\MyCourseService;
use Illuminate\Http\Request;

class MyCourseController extends ApiController
{
    protected $courseService;

    public function __construct(MyCourseService $courseService)
    {
        $this->courseService = $courseService;
        // $this->middleware('auth:api');
    }

    /**
     * 获取课程列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['name']);

        $data = $this->courseService->getPaginatedCourses($perPage, $filters);

        return $this->success($data);
    }

    /**
     * 获取单个课程信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->courseService->show($id);

        return $this->success($data);
    }
}
