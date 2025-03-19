<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function index(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $authUser): bool
    {
        return $authUser->type === 'admin' || request()->user_id == $authUser->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, Task $task): bool
    {
        return $authUser->id === $task->user_id || $authUser->type === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, Task $task): bool
    {
        return $authUser->id === $task->user_id || $authUser->type === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
