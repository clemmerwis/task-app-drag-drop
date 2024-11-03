<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name'
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Get tasks that the project owns
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The "booted" method is a Laravel v8+ lifecycle hook that runs
     * after the model and all its traits have booted (better than boot())
     */
    protected static function booted()
    {
        static::creating(function ($project) {
            $project->slug = Str::slug($project->name);
        });
    }
}
