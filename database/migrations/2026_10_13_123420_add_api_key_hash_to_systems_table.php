<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->char('api_key_hash', 64)
                ->nullable()
                ->unique()
                ->after('api_key');
        });

        DB::table('systems')
            ->select(['id', 'api_key'])
            ->orderBy('id')
            ->chunk(100, function ($systems): void {
                foreach ($systems as $system) {
                    try {
                        $apiKey = Crypt::decryptString($system->api_key);
                    } catch (DecryptException) {
                        continue;
                    }

                    DB::table('systems')
                        ->where('id', $system->id)
                        ->update(['api_key_hash' => hash('sha256', $apiKey)]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->dropUnique(['api_key_hash']);
            $table->dropColumn('api_key_hash');
        });
    }
};
