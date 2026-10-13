<?php

namespace App\Http\Requests;

use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class IndexTicketRequest extends SourceSystemRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'external_user_id' => ['nullable', 'string', 'max:100'],
            'status_code' => ['nullable', 'string', 'max:50', Rule::exists(TicketStatus::class, 'code')],
            'priority_code' => ['nullable', 'string', 'max:50', Rule::exists(TicketPriority::class, 'code')],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'search.max' => 'عبارت جستجو نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'external_user_id.max' => 'شناسه کاربر نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'status_code.exists' => 'وضعیت انتخاب‌شده معتبر نیست.',
            'priority_code.exists' => 'اولویت انتخاب‌شده معتبر نیست.',
            'page.integer' => 'شماره صفحه باید عدد صحیح باشد.',
            'page.min' => 'شماره صفحه باید حداقل ۱ باشد.',
            'per_page.integer' => 'تعداد نتایج هر صفحه باید عدد صحیح باشد.',
            'per_page.min' => 'تعداد نتایج هر صفحه باید حداقل ۱ باشد.',
            'per_page.max' => 'تعداد نتایج هر صفحه نمی‌تواند بیشتر از ۱۰۰ باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $search = $this->string('search')->trim()->toString();
        $externalUserId = $this->string('external_user_id')->trim()->toString();
        $statusCode = $this->string('status_code')->trim()->lower()->toString();
        $priorityCode = $this->string('priority_code')->trim()->lower()->toString();

        $this->merge([
            'search' => $search !== '' ? $search : null,
            'external_user_id' => $externalUserId !== '' ? $externalUserId : null,
            'status_code' => $statusCode !== '' ? $statusCode : null,
            'priority_code' => $priorityCode !== '' ? $priorityCode : null,
        ]);
    }
}
