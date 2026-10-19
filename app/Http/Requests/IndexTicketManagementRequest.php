<?php

namespace App\Http\Requests;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTicketManagementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Ticket::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'source_system_id' => ['nullable', 'uuid', Rule::exists(System::class, 'id')],
            'status_id' => ['nullable', 'uuid', Rule::exists(TicketStatus::class, 'id')],
            'priority_id' => ['nullable', 'uuid', Rule::exists(TicketPriority::class, 'id')],
            'assigned_to' => [
                'nullable',
                Rule::when(
                    $this->input('assigned_to') === 'unassigned',
                    [Rule::in(['unassigned'])],
                    ['uuid', Rule::exists(User::class, 'id')->where('is_active', true)],
                ),
            ],
            'per_page' => ['sometimes', 'integer', Rule::in([10, 20, 50])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'search.max' => 'عبارت جستجو نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'source_system_id.exists' => 'سامانه انتخاب‌شده معتبر نیست.',
            'status_id.exists' => 'وضعیت انتخاب‌شده معتبر نیست.',
            'priority_id.exists' => 'اولویت انتخاب‌شده معتبر نیست.',
            'assigned_to.exists' => 'کارشناس انتخاب‌شده معتبر یا فعال نیست.',
            'assigned_to.in' => 'مقدار فیلتر کارشناس معتبر نیست.',
            'per_page.in' => 'تعداد نمایش باید یکی از مقادیر ۱۰، ۲۰ یا ۵۰ باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $search = $this->string('search')->trim()->toString();

        $this->merge([
            'search' => $search !== '' ? $search : null,
        ]);
    }
}
