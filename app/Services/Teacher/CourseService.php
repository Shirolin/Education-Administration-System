<?php

namespace App\Services\Teacher;

use App\Models\Course\Course;
use App\Services\BaseService;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CourseService extends BaseService
{
    /**
     * 分页获取课程列表
     * @param int $perPage
     * @param array $filters
     */
    public function getPaginatedCourses($perPage = self::DEFAULT_PER_PAGE, $filters = []): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Course::class); // 检查用户是否有权限查看课程列表

        $query = Course::query();
        $query->with('subCourses');

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "{$filters['name']}%");
        }

        return $query->withCount(['subCourses', 'students'])->paginate($perPage);
    }

    /**
     * 获取单个课程信息
     * @throws Throwable
     */
    public function show(int $id): Course
    {
        $course = $this->findCourseOrFail($id);
        $course->load('subCourses');

        return $course;
    }

    /**
     * 创建课程及子课程
     *
     * @param array $courseData 课程数据
     * @param array $subCoursesData 子课程数据数组
     * @param array $studentIds 学生ID数组
     * @throws Throwable
     */
    public function createCourse(array $courseData, array $subCoursesData = [], array $studentIds = []): bool
    {
        Gate::authorize('create', Course::class); // 检查用户是否有权限创建课程

        try {
            DB::transaction(function () use ($courseData, $subCoursesData, $studentIds) {
                $course = Course::create($courseData);
                foreach ($subCoursesData as $subCourseData) {
                    $subCourseData['fee'] = $courseData['unit_fee']; // 设置子课程的费用为主课程的单价
                    $course->subCourses()->create($subCourseData);
                }
                // 创建课程后，将学生加入课程
                $course->students()->attach($studentIds);
            });
        } catch (\Exception $e) {
            Log::error('创建课程失败', [
                'course_data' => $courseData,
                'sub_courses_data' => $subCoursesData,
                'message' => $e->getMessage(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * 更新课程及子课程
     *
     * @param int $id 课程ID
     * @param array $courseData 课程数据
     * @param array $subCoursesData 子课程数据数组(全量)
     * @param array $studentIds 学生ID数组
     * @throws Throwable
     */
    public function updateCourse(int $id, array $courseData, array $subCoursesData = [], array $studentIds = []): bool
    {
        $course = $this->findCourseOrFail($id);

        Gate::authorize('update', $course); // 检查用户是否有权限更新课程

        // 子课程数据处理：
        // 传入的是全量，需要判断是更新已存在的子课程还是创建新的子课程还是删除已存在的子课程
        try {
            DB::transaction(function () use ($course, $courseData, $subCoursesData, $studentIds) {
                $course->update($courseData);

                // 更新子课程(新增、更新、删除)

                $subCourseIds = array_column($subCoursesData, 'id');
                $course->subCourses()->whereNotIn('id', $subCourseIds)->delete();

                // 更新课程后，将学生加入课程
                $course->students()->sync($studentIds);

                foreach ($subCoursesData as $subCourseData) {
                    $subCourseData['fee'] = $courseData['unit_fee']; // 设置子课程的费用为主课程的单价
                    if (isset($subCourseData['id'])) {
                        $course->subCourses()->where('id', $subCourseData['id'])->update($subCourseData);
                    } else {
                        $course->subCourses()->create($subCourseData);
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('更新课程失败', [
                'id' => $id,
                'course_data' => $courseData,
                'sub_courses_data' => $subCoursesData,
                'message' => $e->getMessage(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * 删除课程及所有子课程
     * @throws Throwable
     */
    public function deleteCourse(int $id): bool
    {
        $course = $this->findCourseOrFail($id);

        Gate::authorize('delete', $course); // 检查用户是否有权限删除课程

        try {
            DB::transaction(function () use ($course) {
                $course->subCourses()->delete(); // 先删除子课程
                $course->delete(); // 再删除课程
            });
        } catch (\Exception $e) {
            Log::error('删除课程失败', ['id' => $id, 'message' => $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * 根据ID查找课程，如果找不到则抛出异常
     *
     * @return Course
     * @throws ModelNotFoundException
     */
    public function findCourseOrFail(int $id): Course
    {
        $course = Course::withCount(['subCourses', 'students'])->findOrFail($id);

        Gate::authorize('view', $course); // 检查用户是否有权限查看课程

        return $course;
    }
}
