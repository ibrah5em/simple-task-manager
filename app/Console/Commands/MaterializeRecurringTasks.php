<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\RecurrenceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MaterializeRecurringTasks extends Command
{
    protected $signature   = 'tasks:materialize-recurring';
    protected $description = 'Pre-generate recurring task instances for the next 7 days';

    public function handle(RecurrenceService $recurr): int
    {
        $horizon = today()->addDays(7);

        $roots = Task::whereNotNull('recurrence_rule')
            ->whereNull('recurrence_parent_id')
            ->with('categories')
            ->get();

        $created = 0;

        foreach ($roots as $root) {
            $dtstart     = $root->due_date ?? today();
            $occurrences = $recurr->occurrencesInRange(
                $root->recurrence_rule,
                today(),
                $horizon,
                $dtstart
            );

            if (empty($occurrences)) {
                continue;
            }

            DB::transaction(function () use ($root, $occurrences, &$created) {
                foreach ($occurrences as $date) {
                    $alreadyExists = Task::where('recurrence_parent_id', $root->id)
                        ->whereDate('due_date', $date->toDateString())
                        ->exists();

                    if ($alreadyExists) {
                        continue;
                    }

                    $child = Task::create([
                        'title'                => $root->title,
                        'description'          => $root->description,
                        'due_date'             => $date,
                        'priority'             => $root->priority,
                        'user_id'              => $root->user_id,
                        'is_completed'         => false,
                        'recurrence_parent_id' => $root->id,
                        'recurrence_rule'      => null,
                    ]);

                    if ($root->categories->isNotEmpty()) {
                        $child->categories()->sync($root->categories->pluck('id'));
                    }

                    $created++;
                }
            });
        }

        $this->info("Materialized {$created} recurring task instance(s).");

        return self::SUCCESS;
    }
}
