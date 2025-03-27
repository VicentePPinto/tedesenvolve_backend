<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaskService
{
    public function getUserTasksInPeriod(int $userId, Carbon $startDate, Carbon $endDate): Collection
    {
        return Task::where('user_id', $userId)
            ->with(['state', 'category'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
