<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'جدید',
                'code' => 'new',
                'description' => 'تیکت به‌تازگی ثبت شده است.',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'باز',
                'code' => 'open',
                'description' => 'تیکت بررسی شده و آماده پیگیری است.',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'در حال رسیدگی',
                'code' => 'in_progress',
                'description' => 'رسیدگی به تیکت در حال انجام است.',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'در انتظار',
                'code' => 'pending',
                'description' => 'تیکت منتظر پاسخ یا اقدام تکمیلی است.',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'حل شده',
                'code' => 'resolved',
                'description' => 'راه‌حل ارائه شده و تیکت حل شده است.',
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'بسته شده',
                'code' => 'closed',
                'description' => 'فرایند رسیدگی به تیکت پایان یافته است.',
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'لغو شده',
                'code' => 'cancelled',
                'description' => 'تیکت لغو شده است.',
                'sort_order' => 70,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            TicketStatus::query()->updateOrCreate(
                ['code' => $status['code']],
                $status,
            );
        }
    }
}
