<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaskInputParser
{
    private const DAYS_FULL  = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    private const DAYS_SHORT = ['mon','tue','wed','thu','fri','sat','sun'];
    private const DAY_MAP    = [
        'mon'=>1,'monday'=>1,'tue'=>2,'tuesday'=>2,'wed'=>3,'wednesday'=>3,
        'thu'=>4,'thursday'=>4,'fri'=>5,'friday'=>5,'sat'=>6,'saturday'=>6,
        'sun'=>0,'sunday'=>0,
    ];

    /**
     * Parse raw quick-add string into structured task fields.
     *
     * @param  string          $raw            Raw user input
     * @param  Collection|null $userCategories User's existing Category models (id,name)
     * @return array{title:string, due_date:string|null, priority:string, categories:array, unknown_categories:array}
     */
    public function parse(string $raw, ?Collection $userCategories = null): array
    {
        $work     = trim($raw);
        $priority = 'medium';
        $dueDate  = null;
        $catNames = [];
        $tz       = config('app.timezone', 'UTC');
        $now      = Carbon::now($tz);

        // ── 1. Priority ──────────────────────────────────────────────────────
        if (preg_match('/\!(high|med(?:ium)?|low)\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $token    = strtolower($m[1][0]);
            $priority = match(true) {
                str_starts_with($token, 'h') => 'high',
                str_starts_with($token, 'l') => 'low',
                default                      => 'medium',
            };
            $work = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }

        // ── 2. Categories (#tag) ─────────────────────────────────────────────
        if (preg_match_all('/#(\w+)/i', $work, $ms)) {
            $catNames = array_map('strtolower', $ms[1]);
            $work     = preg_replace('/#\w+\s*/i', '', $work);
        }

        // ── 3. Time (extract first so we can apply to date) ──────────────────
        $hour = null;
        $min  = 0;
        if (preg_match('/\b(\d{1,2})(?::(\d{2}))?\s*(am|pm)\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $h    = (int)$m[1][0];
            $min  = isset($m[2][0]) && $m[2][0] !== '' ? (int)$m[2][0] : 0;
            $ampm = strtolower($m[3][0]);
            $hour = ($ampm === 'pm' && $h < 12) ? $h + 12 : (($ampm === 'am' && $h === 12) ? 0 : $h);
            $work = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        } elseif (preg_match('/\b(\d{2}):(\d{2})\b/', $work, $m, PREG_OFFSET_CAPTURE)) {
            $hour = (int)$m[1][0];
            $min  = (int)$m[2][0];
            $work = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }

        // ── 4. Date phrases ───────────────────────────────────────────────────
        $days     = implode('|', array_merge(self::DAYS_FULL, self::DAYS_SHORT));
        $months   = 'jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|jun(?:e)?|jul(?:y)?|aug(?:ust)?|sep(?:tember)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?';

        // today / tonight
        if (preg_match('/\b(today|tonight)\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $dueDate = $now->copy()->startOfDay();
            $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }
        // tomorrow
        elseif (preg_match('/\btomorrow\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $dueDate = $now->copy()->addDay()->startOfDay();
            $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }
        // next <weekday>
        elseif (preg_match('/\bnext\s+(' . $days . ')\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $dayNum  = self::DAY_MAP[strtolower($m[1][0])];
            $dueDate = $now->copy()->next($dayNum)->startOfDay();
            $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }
        // <weekday> (standalone)
        elseif (preg_match('/\b(' . $days . ')\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $dayNum  = self::DAY_MAP[strtolower($m[1][0])];
            $dueDate = $now->copy()->startOfDay();
            if ($dueDate->dayOfWeek !== $dayNum || $now->hour >= 23) {
                $dueDate = $now->copy()->next($dayNum)->startOfDay();
            }
            $work = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }
        // in N days/weeks/months
        elseif (preg_match('/\bin\s+(\d+)\s+(days?|weeks?|months?)\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            $n       = (int)$m[1][0];
            $unit    = rtrim(strtolower($m[2][0]), 's');
            $dueDate = match($unit) {
                'week'  => $now->copy()->addWeeks($n)->startOfDay(),
                'month' => $now->copy()->addMonths($n)->startOfDay(),
                default => $now->copy()->addDays($n)->startOfDay(),
            };
            $work = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
        }
        // "jul 4" or "4 jul"
        elseif (preg_match('/\b(' . $months . ')\s+(\d{1,2})\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            try {
                $parsed  = Carbon::parse($m[1][0] . ' ' . $m[2][0], $tz)->startOfDay();
                if ($parsed->isPast()) $parsed->addYear();
                $dueDate = $parsed;
                $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
            } catch (\Exception) {}
        }
        elseif (preg_match('/\b(\d{1,2})\s+(' . $months . ')\b/i', $work, $m, PREG_OFFSET_CAPTURE)) {
            try {
                $parsed  = Carbon::parse($m[1][0] . ' ' . $m[2][0], $tz)->startOfDay();
                if ($parsed->isPast()) $parsed->addYear();
                $dueDate = $parsed;
                $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
            } catch (\Exception) {}
        }
        // ISO date YYYY-MM-DD
        elseif (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $work, $m, PREG_OFFSET_CAPTURE)) {
            try {
                $dueDate = Carbon::createFromFormat('Y-m-d', $m[1][0], $tz)->startOfDay();
                $work    = $this->removeAt($work, $m[0][1], strlen($m[0][0]));
            } catch (\Exception) {}
        }

        // Apply extracted time to the date
        if ($dueDate !== null && $hour !== null) {
            $dueDate->setTime($hour, $min);
        } elseif ($dueDate === null && $hour !== null) {
            // Time without date → today
            $dueDate = $now->copy()->setTime($hour, $min);
            if ($dueDate->isPast()) {
                $dueDate->addDay();
            }
        }

        // ── 5. Title (cleaned remainder) ─────────────────────────────────────
        $title = trim(preg_replace('/\s{2,}/', ' ', $work));

        // ── 6. Resolve categories against user's existing list ───────────────
        $knownIds    = [];
        $unknownCats = [];
        if ($userCategories) {
            $index = $userCategories->keyBy(fn($c) => strtolower($c->name));
            foreach ($catNames as $name) {
                if ($index->has($name)) {
                    $knownIds[] = $index->get($name)->id;
                } else {
                    $unknownCats[] = $name;
                }
            }
        }

        return [
            'title'              => $title,
            'due_date'           => $dueDate?->toDateTimeString(),
            'due_date_human'     => $dueDate?->format('D, M j' . ($hour !== null ? ' g:ia' : '')),
            'priority'           => $priority,
            'categories'         => $knownIds,
            'category_names'     => $catNames,
            'unknown_categories' => $unknownCats,
        ];
    }

    private function removeAt(string $str, int $offset, int $length): string
    {
        return trim(preg_replace('/\s{2,}/', ' ', substr_replace($str, ' ', $offset, $length)));
    }
}
