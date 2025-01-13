<?php

namespace App\Services\Teacher;

use App\Models\Role\Student;
use App\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentService extends BaseService
{
    /**
     * 分页获取学生列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedStudents($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        $query = Student::query();

        if (isset($filters['nickname'])) {
            $query->where('nickname', 'LIKE', "{$filters['nickname']}%");
        }

        return $query->paginate($perPage);
    }
}
