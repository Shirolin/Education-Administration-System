<?php

namespace App\Http\Controllers\Teacher\Course;

use App\Http\Controllers\ApiController;
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
     * 创建课程
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $data = $this->courseService->store();

        return $this->success($data, '创建成功', 201);
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

    /**
     * 更新课程信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $data = $this->courseService->update($id);

        return $this->success($data, '更新成功');
    }

    /**
     * 删除课程
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->courseService->destroy($id);

        return $this->success($data, '删除成功');
    }
}
