<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class IndexTicketMessageRequest extends SourceSystemRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'page.integer' => 'شماره صفحه باید عدد صحیح باشد.',
            'page.min' => 'شماره صفحه باید حداقل ۱ باشد.',
            'per_page.integer' => 'تعداد نتایج هر صفحه باید عدد صحیح باشد.',
            'per_page.min' => 'تعداد نتایج هر صفحه باید حداقل ۱ باشد.',
            'per_page.max' => 'تعداد نتایج هر صفحه نمی‌تواند بیشتر از ۱۰۰ باشد.',
        ];
    }
}
