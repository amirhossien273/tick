<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'کم',
                'code' => 'low',
                'description' => 'تیکت با اولویت پایین و قابل رسیدگی در روال عادی است.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'عادی',
                'code' => 'normal',
                'description' => 'تیکت دارای اولویت عادی است.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'زیاد',
                'code' => 'high',
                'description' => 'تیکت باید با اولویت بیشتری پیگیری شود.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'فوری',
                'code' => 'urgent',
                'description' => 'تیکت نیازمند رسیدگی فوری است.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'بحرانی',
                'code' => 'critical',
                'description' => 'تیکت بحرانی است و باید در بالاترین اولویت رسیدگی شود.',
                'sort_order' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($priorities as $priority) {
            TicketPriority::query()->updateOrCreate(
                ['code' => $priority['code']],
                $priority,
            );
        }
    }
}
