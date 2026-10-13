<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreTicketMessageRequest extends SourceSystemRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:20000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'message.required' => 'متن پیام الزامی است.',
            'message.max' => 'متن پیام نمی‌تواند بیشتر از ۲۰۰۰۰ کاراکتر باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => $this->string('message')->trim()->toString(),
        ]);
    }
}
