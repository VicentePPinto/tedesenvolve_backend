<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function index(User $authUser, Task $task): bool
    {
        return false;

        return $this->canAccessTask($authUser, $task);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, array $data): bool
    {
        return $user->type === 'admin' || $user->id === $data['user_id'];
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Task $task): bool
    {

        $authUser = auth()->user();

        return $this->canAccessTask($authUser, $task);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewAny(): bool
    {

        $authUser = auth()->user();

        return $authUser->type === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, Task $task): bool
    {
        return $this->canAccessTask($authUser, $task);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, Task $task): bool
    {
        return $this->canAccessTask($authUser, $task);
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

    public function getTasksInPeriod(User $user, array $data): bool
    {

        return $user->type === 'admin' || $user->id === $data['user_id'];
    }

    /**
     * Regra centralizada de acesso a uma tarefa.
     */
    private function canAccessTask(User $user, Task $task): bool
    {
        return $user->type == 'admin' || $user->id === $task->user_id;
    }
}
