<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskState extends Model
{
    use HasFactory;

    protected $fillable = ['state', 'company_id'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_state_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
