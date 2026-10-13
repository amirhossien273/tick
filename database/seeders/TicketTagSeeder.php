<?php

namespace Database\Seeders;

use App\Models\TicketTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketTagSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'نیازمند پیگیری',
                'slug' => 'follow-up',
                'color' => '#F59E0B',
                'description' => 'تیکت نیازمند پیگیری مجدد توسط تیم پشتیبانی است.',
                'is_active' => true,
            ],
            [
                'name' => 'شکایت مشتری',
                'slug' => 'customer-complaint',
                'color' => '#DC2626',
                'description' => 'تیکت شامل شکایت یا نارضایتی مشتری است.',
                'is_active' => true,
            ],
            [
                'name' => 'مشکل فنی',
                'slug' => 'technical-issue',
                'color' => '#2563EB',
                'description' => 'تیکت به یک مشکل فنی مرتبط است.',
                'is_active' => true,
            ],
            [
                'name' => 'امور مالی',
                'slug' => 'billing',
                'color' => '#16A34A',
                'description' => 'تیکت مربوط به پرداخت، صورتحساب یا مسائل مالی است.',
                'is_active' => true,
            ],
            [
                'name' => 'مشتری ویژه',
                'slug' => 'vip-customer',
                'color' => '#7C3AED',
                'description' => 'تیکت متعلق به مشتری ویژه است.',
                'is_active' => true,
            ],
            [
                'name' => 'گزارش خطا',
                'slug' => 'bug-report',
                'color' => '#EA580C',
                'description' => 'تیکت برای گزارش خطای نرم‌افزاری ثبت شده است.',
                'is_active' => true,
            ],
            [
                'name' => 'درخواست قابلیت',
                'slug' => 'feature-request',
                'color' => '#0891B2',
                'description' => 'تیکت شامل پیشنهاد یا درخواست قابلیت جدید است.',
                'is_active' => true,
            ],
        ];

        foreach ($tags as $tag) {
            TicketTag::query()->updateOrCreate(
                ['slug' => $tag['slug']],
                $tag,
            );
        }
    }
}
