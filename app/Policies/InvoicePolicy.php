<?php

namespace App\Policies;

use App\Models\Invoice\Invoice;
use App\Models\Role\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return ($user->isTeacher() || $user->isStudent()) ? Response::allow()
            : Response::deny('你没有权限查看账单列表');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): Response
    {
        // 创建者或者对应学生可以查看
        return ($invoice->isCreator($user->id) || $invoice->isStudent($user->id)) ? Response::allow()
            : Response::deny('你没有权限查看该账单');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isTeacher() ? Response::allow()
            : Response::deny('你没有权限创建账单');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): Response
    {
        // 创建者或者对应学生可以更新
        return ($invoice->isCreator($user->id) || $invoice->isStudent($user->id)) ? Response::allow()
            : Response::deny('你没有权限更新该账单');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): Response
    {
        return $invoice->isCreator($user->id) ? Response::allow()
            : Response::deny('你没有权限删除该账单');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): Response
    {
        return $invoice->isCreator($user->id) ? Response::allow()
            : Response::deny('你没有权限恢复该账单');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): Response
    {
        return $invoice->isCreator($user->id) ? Response::allow()
            : Response::deny('你没有权限永久删除该账单');
    }

    /**
     * Determine whether the user can send the model.
     */
    public function send(User $user, Invoice $invoice): Response
    {
        return $invoice->isCreator($user->id) ? Response::allow()
            : Response::deny('你没有权限发送该账单');
    }
}
