<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'فنی',
                'code' => 'technical',
                'description' => 'مشکلات و درخواست‌های فنی',
                'sort_order' => 10,
                'children' => [
                    ['name' => 'نرم‌افزار', 'code' => 'software', 'description' => 'مشکلات و درخواست‌های نرم‌افزاری', 'sort_order' => 10],
                    ['name' => 'سخت‌افزار', 'code' => 'hardware', 'description' => 'مشکلات و درخواست‌های سخت‌افزاری', 'sort_order' => 20],
                    ['name' => 'شبکه', 'code' => 'network', 'description' => 'مشکلات شبکه و اتصال', 'sort_order' => 30],
                ],
            ],
            [
                'name' => 'مالی',
                'code' => 'financial',
                'description' => 'درخواست‌ها و مشکلات مالی',
                'sort_order' => 20,
                'children' => [
                    ['name' => 'صورتحساب', 'code' => 'invoice', 'description' => 'پرسش‌ها و مشکلات صورتحساب', 'sort_order' => 10],
                    ['name' => 'پرداخت', 'code' => 'payment', 'description' => 'پرسش‌ها و مشکلات پرداخت', 'sort_order' => 20],
                ],
            ],
            [
                'name' => 'حساب کاربری',
                'code' => 'account',
                'description' => 'درخواست‌های مرتبط با حساب کاربری',
                'sort_order' => 30,
                'children' => [
                    ['name' => 'دسترسی', 'code' => 'access', 'description' => 'مشکلات ورود و سطح دسترسی', 'sort_order' => 10],
                    ['name' => 'اطلاعات پروفایل', 'code' => 'profile', 'description' => 'درخواست‌های مرتبط با اطلاعات پروفایل', 'sort_order' => 20],
                ],
            ],
            [
                'name' => 'عمومی',
                'code' => 'general',
                'description' => 'سایر درخواست‌ها و پرسش‌های عمومی',
                'sort_order' => 40,
                'children' => [],
            ],
        ];

        foreach ($categories as $category) {
            $children = $category['children'];
            unset($category['children']);

            $parent = TicketCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                [...$category, 'parent_id' => null, 'is_active' => true],
            );

            foreach ($children as $child) {
                TicketCategory::query()->updateOrCreate(
                    ['code' => $child['code']],
                    [...$child, 'parent_id' => $parent->id, 'is_active' => true],
                );
            }
        }
    }
}
