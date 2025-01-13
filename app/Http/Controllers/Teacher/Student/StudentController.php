<?php

namespace App\Http\Controllers\Teacher\Student;

use App\Http\Controllers\ApiController;
use App\Services\Teacher\StudentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentController extends ApiController
{
    protected $studentservice;

    public function __construct(StudentService $studentservice)
    {
        $this->studentservice = $studentservice;
    }

    /**
     * 获取学生列表
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page');
        $filters = $request->only(['nickname']);

        $data = $this->studentservice->getPaginatedStudents($perPage, $filters);

        return $this->success($data);
    }
}
