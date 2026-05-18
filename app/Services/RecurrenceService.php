<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Carbon;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;

class RecurrenceService
{
    private ArrayTransformer $transformer;

    public function __construct()
    {
        $this->transformer = new ArrayTransformer();
    }

    public function presetToRrule(string $preset, ?Carbon $dueDate): ?string
    {
        return match ($preset) {
            'daily'    => 'FREQ=DAILY',
            'weekdays' => 'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR',
            'weekly'   => $this->weeklyRule($dueDate),
            'monthly'  => $this->monthlyRule($dueDate),
            default    => null,
        };
    }

    public function rruleToPreset(string $rrule): string
    {
        return match (true) {
            $rrule === 'FREQ=DAILY'                           => 'daily',
            str_starts_with($rrule, 'FREQ=WEEKLY;BYDAY=MO,TU') => 'weekdays',
            str_starts_with($rrule, 'FREQ=WEEKLY')            => 'weekly',
            str_starts_with($rrule, 'FREQ=MONTHLY')           => 'monthly',
            default                                           => 'custom',
        };
    }

    public function spawnNextOccurrence(Task $rootTask, ?Carbon $afterDate = null): ?Task
    {
        if (!$rootTask->recurrence_rule) {
            return null;
        }

        $dtstart = $rootTask->due_date ?? today();
        $after   = $afterDate ?? today();

        $nextDate = $this->nextOccurrenceAfter($rootTask->recurrence_rule, $after, $dtstart);
        if (!$nextDate) {
            return null;
        }

        $alreadyExists = Task::where('recurrence_parent_id', $rootTask->id)
            ->whereDate('due_date', $nextDate->toDateString())
            ->exists();

        if ($alreadyExists) {
            return null;
        }

        $child = Task::create([
            'title'                => $rootTask->title,
            'description'          => $rootTask->description,
            'due_date'             => $nextDate,
            'priority'             => $rootTask->priority,
            'user_id'              => $rootTask->user_id,
            'is_completed'         => false,
            'recurrence_parent_id' => $rootTask->id,
            'recurrence_rule'      => null,
        ]);

        if ($rootTask->categories->isNotEmpty()) {
            $child->categories()->sync($rootTask->categories->pluck('id'));
        }

        return $child;
    }

    public function occurrencesInRange(string $rrule, Carbon $start, Carbon $end, Carbon $dtstart): array
    {
        try {
            $rule       = new Rule($rrule, $dtstart->toDateTime());
            $constraint = new BetweenConstraint($start->toDateTime(), $end->toDateTime(), true);
            $result     = $this->transformer->transform($rule, $constraint);

            return array_map(
                fn($r) => Carbon::instance($r->getStart()),
                $result->toArray()
            );
        } catch (\Throwable) {
            return [];
        }
    }

    private function nextOccurrenceAfter(string $rrule, Carbon $after, Carbon $dtstart): ?Carbon
    {
        try {
            $rule       = new Rule($rrule, $dtstart->toDateTime());
            $constraint = new AfterConstraint($after->toDateTime(), false);
            $result     = $this->transformer->transform($rule, $constraint);

            if ($result->count() === 0) {
                return null;
            }

            return Carbon::instance($result[0]->getStart());
        } catch (\Throwable) {
            return null;
        }
    }

    private function weeklyRule(?Carbon $dueDate): string
    {
        $days = ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'];
        $day  = $dueDate ? $days[$dueDate->dayOfWeek] : 'MO';
        return "FREQ=WEEKLY;BYDAY={$day}";
    }

    private function monthlyRule(?Carbon $dueDate): string
    {
        $dayOfMonth = $dueDate ? $dueDate->day : 1;
        return "FREQ=MONTHLY;BYMONTHDAY={$dayOfMonth}";
    }
}
