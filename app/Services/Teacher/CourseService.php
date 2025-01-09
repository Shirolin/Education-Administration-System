<?php

namespace App\Services\Teacher;

use App\Models\Course\Course;
use App\Services\BaseService;
use DB;
use Illuminate\Support\Facades\Log;

class CourseService extends BaseService
{
    /**
     * 分页获取课程列表
     * @param int $perPage
     * @param array $filters
     * @return mixed
     */
    public function getPaginatedCourses($perPage = self::DEFAULT_PER_PAGE, $filters = [])
    {
        $query = Course::query();
        $query->with('subCourses');

        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', "{$filters['name']}%");
        }

        return $query->withCount(['subCourses', 'students'])->paginate($perPage);
    }

    /**
     * 获取单个课程信息
     * @param int $id
     * @return array
     * @throws Throwable
     */
    public function show(int $id): array
    {
        $course = $this->findCourseOrFail($id);

        return [
            'course' => $course,
            'sub_courses' => $course->subCourses,
        ];
    }

    /**
     * 创建课程及子课程
     *
     * @param array $courseData 课程数据
     * @param array $subCoursesData 子课程数据数组
     * @return bool
     * @throws Throwable
     */
    public function createCourse(array $courseData, array $subCoursesData): bool
    {
        try {
            DB::transaction(function () use ($courseData, $subCoursesData) {
                $course = Course::create($courseData);
                foreach ($subCoursesData as $subCourseData) {
                    $course->subCourses()->create($subCourseData);
                }
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
     * 删除课程及所有子课程
     *
     * @param int $id 课程ID
     * @return bool
     */
    public function deleteCourse(int $id): bool
    {
        $course = $this->findCourseOrFail($id);

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
     * 更新课程及子课程
     *
     * @param int $id 课程ID
     * @param array $courseData 课程数据
     * @param array $subCoursesData 子课程数据数组(全量)
     * @return bool
     * @throws Throwable
     */
    public function updateCourse(int $id, array $courseData, array $subCoursesData): bool
    {
        $course = $this->findCourseOrFail($id);

        // 子课程数据处理：
        // 传入的是全量，需要判断是更新已存在的子课程还是创建新的子课程还是删除已存在的子课程
        try {
            DB::transaction(function () use ($course, $courseData, $subCoursesData) {
                $course->update($courseData);

                // 更新子课程(新增、更新、删除)

                $subCourseIds = array_column($subCoursesData, 'id');
                $course->subCourses()->whereNotIn('id', $subCourseIds)->delete();

                foreach ($subCoursesData as $subCourseData) {
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
     * 根据ID查找课程，如果找不到则抛出异常
     *
     * @param int $id 课程ID
     * @return Course
     * @throws ModelNotFoundException
     */
    public function findCourseOrFail(int $id): Course
    {
        return Course::withCount(['subCourses', 'students'])->findOrFail($id);
    }
}
