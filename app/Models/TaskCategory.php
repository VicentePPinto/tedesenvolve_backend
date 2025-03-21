<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }
}
