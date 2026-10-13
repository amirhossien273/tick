<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_code_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedBigInteger('last_number')->default(0);
        });

        $sequences = [];

        DB::table('systems')
            ->select(['id', 'code'])
            ->orderBy('id')
            ->each(function ($system) use (&$sequences): void {
                if (preg_match('/^SYS-(\d{4})\d{4}-(\d+)$/', $system->code, $matches) !== 1) {
                    return;
                }

                $year = (int) $matches[1];
                $sequences[$year] = max($sequences[$year] ?? 0, (int) $matches[2]);
            });

        foreach ($sequences as $year => $lastNumber) {
            DB::table('system_code_sequences')->insert([
                'year' => $year,
                'last_number' => $lastNumber,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_code_sequences');
    }
};
