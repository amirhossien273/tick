<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TicketStatusSeeder::class);
        $this->call(TicketPrioritySeeder::class);
        $this->call(TicketCategorySeeder::class);
        $this->call(TicketTagSeeder::class);
        $this->call(AdminUserSeeder::class);
    }
}
