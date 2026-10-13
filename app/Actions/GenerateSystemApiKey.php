<?php

namespace App\Actions;

use Illuminate\Support\Str;

class GenerateSystemApiKey
{
    public function handle(): string
    {
        return 'tkt_'.Str::random(56);
    }
}
