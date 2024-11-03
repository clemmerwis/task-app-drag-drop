<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'project_id'
    ];

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The "booted" method is a Laravel lifecycle hook
     */
    protected static function booted()
    {
        // This is a Laravel model event
        static::creating(function ($task) {
            if (!$task->priority) {
                // Get highest priority in the same project and add 1, or start at 1 if no tasks exist
                $task->priority = self::query()
                    ->where('project_id', $task->project_id)
                    ->max('priority') + 1 ?? 1;
            }
        });
    }

    /**
     * Update priority and reorder other tasks within the same project
     */
    public function updatePriority(int $newPriority)
    {
        // Early return for efficiency
        if ($this->priority === $newPriority) {
            return;
        }

        // Transaction wrapper-- ensures updates only execute if all are successful
        DB::transaction(function() use ($newPriority) {
            if ($newPriority < $this->priority) {
                self::query()
                    ->where('project_id', $this->project_id)
                    ->where('priority', '>=', $newPriority)
                    ->where('priority', '<', $this->priority)
                    ->increment('priority');
            } 
            else if ($newPriority > $this->priority) {
                self::query()
                    ->where('project_id', $this->project_id)
                    ->where('priority', '<=', $newPriority)
                    ->where('priority', '>', $this->priority)
                    ->decrement('priority');
            }

            $this->priority = $newPriority;
            $this->save();
        });
    }
}