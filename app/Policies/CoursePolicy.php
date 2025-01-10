<?php

namespace App\Policies;

use App\Models\Course\Course;
use App\Models\Role\User;
use Illuminate\Auth\Access\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // TODO.限定学生只能查看自己的课程，老师可以查看所有课程
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isTeacher() ? Response::allow()
            : Response::denyWithStatus(SymfonyResponse::HTTP_FORBIDDEN, '你没有权限创建课程');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): Response
    {
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::denyWithStatus(SymfonyResponse::HTTP_FORBIDDEN, '你没有权限修改该课程');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): Response
    {
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::denyWithStatus(SymfonyResponse::HTTP_FORBIDDEN, '你没有权限删除该课程');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): Response
    {
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::denyWithStatus(SymfonyResponse::HTTP_FORBIDDEN, '你没有权限恢复该课程');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): Response
    {
        return $course->isTeacher($user->id) ? Response::allow()
            : Response::denyWithStatus(SymfonyResponse::HTTP_FORBIDDEN, '你没有权限永久删除该课程');
    }
}
