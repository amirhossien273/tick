<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ShowTicketRequest extends SourceSystemRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [];
    }
}
