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
        if (!$data) {
            return $this->error('课程不存在', $data);
        }

        return $this->success($data);
    }
}
