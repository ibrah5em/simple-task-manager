<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'user1@example.com')->first();

        $tasks = [
            [
                'title'        => 'Prepare project report',
                'description'  => 'Weekly project progress report',
                'due_date'     => '2025-06-15',
                'is_completed' => false,
            ],
            [
                'title'        => 'Review Laravel code',
                'description'  => 'Review colleagues\' code',
                'due_date'     => '2025-06-10',
                'is_completed' => true,
            ],
            [
                'title'        => 'Team Meeting',
                'description'  => 'Discuss next phase requirements',
                'due_date'     => '2025-06-12',
                'is_completed' => false,
            ],
            [
                'title'        => 'Update API documentation',
                'description'  => 'Document new API interfaces',
                'due_date'     => '2025-06-08',
                'is_completed' => true,
            ],
        ];

        foreach ($tasks as $data) {
            $user1->tasks()->create($data);
        }
    }
}
