<?php

namespace App\Policies;

use App\Models\Course\Course;
use App\Models\Role\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return ($user->isTeacher() || $user->isStudent()) ? Response::allow()
            : Response::deny('你没有权限查看课程列表');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): Response
    {
        // 限定学生只能查看自己参加的课程，老师可以查看所有课程
        return ($course->isTeacher($user->id) || $course->isStudent($user->id)) ? Response::allow()
            : Response::deny('你没有权限查看该课程');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        // 只有老师可以创建课程
        return $user->isTeacher() ? Response::allow()
            : Response::deny('你没有权限创建课程');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): Response
    {
        // 只有该课程的老师可以修改课程
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::deny('你没有权限修改该课程');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): Response
    {
        // 只有该课程的老师可以删除课程
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::deny('你没有权限删除该课程');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): Response
    {
        // 只有该课程的老师可以恢复课程
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::deny('你没有权限恢复该课程');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): Response
    {
        // 只有该课程的老师可以永久删除课程
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::deny('你没有权限永久删除该课程');
    }
}
