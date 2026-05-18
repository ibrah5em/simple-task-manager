<?php

namespace App\Models;

use App\Models\Subtask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'due_date', 'is_completed', 'priority', 'user_id',
        'recurrence_rule', 'recurrence_parent_id',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('position')->orderBy('id');
    }

    public function recurrenceParent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'recurrence_parent_id');
    }

    public function recurrenceChildren(): HasMany
    {
        return $this->hasMany(Task::class, 'recurrence_parent_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($filters['status'])) {
            match ($filters['status']) {
                'active'    => $query->where('is_completed', false),
                'completed' => $query->where('is_completed', true),
                'overdue'   => $query->where('is_completed', false)->whereDate('due_date', '<', today()),
                'week'      => $query->where('is_completed', false)
                                     ->whereDate('due_date', '>=', today())
                                     ->whereDate('due_date', '<=', today()->addDays(7)),
                default     => null,
            };
        }

        if (!empty($filters['category'])) {
            $catId = $filters['category'];
            $query->whereHas('categories', fn (Builder $q) => $q->where('categories.id', $catId));
        }

        return $query;
    }

    public function scopeSort(Builder $query, string $sort = ''): Builder
    {
        return match ($sort) {
            'priority'  => $query->orderByRaw("FIELD(priority,'high','medium','low')"),
            'due_asc'   => $query->orderByRaw("due_date IS NULL, due_date ASC"),
            'due_desc'  => $query->orderBy('due_date', 'desc'),
            'newest'    => $query->orderBy('created_at', 'desc'),
            default     => $query->orderByRaw("due_date IS NULL, due_date ASC")
                                  ->orderByRaw("FIELD(priority,'high','medium','low')")
                                  ->orderBy('created_at', 'desc'),
        };
    }
}
