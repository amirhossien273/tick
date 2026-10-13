<?php

namespace App\Http\Requests;

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends SourceSystemRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'external_user_id' => ['required', 'string', 'max:100'],
            'external_ticket_id' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::exists(TicketPriority::class, 'code')
                    ->where(fn (Builder $query): Builder => $query->where('is_active', true)),
            ],
            'category_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::exists(TicketCategory::class, 'code')
                    ->where(fn (Builder $query): Builder => $query->where('is_active', true)),
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'external_user_id.required' => 'شناسه کاربر در سامانه مبدأ الزامی است.',
            'external_user_id.max' => 'شناسه کاربر نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'external_ticket_id.max' => 'شناسه تیکت مبدأ نمی‌تواند بیشتر از ۱۰۰ کاراکتر باشد.',
            'subject.required' => 'عنوان تیکت الزامی است.',
            'subject.max' => 'عنوان تیکت نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            'description.max' => 'شرح تیکت نمی‌تواند بیشتر از ۱۰۰۰۰ کاراکتر باشد.',
            'priority_code.exists' => 'اولویت انتخاب‌شده معتبر یا فعال نیست.',
            'category_code.exists' => 'دسته‌بندی انتخاب‌شده معتبر یا فعال نیست.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $externalTicketId = $this->string('external_ticket_id')->trim()->toString();
        $description = $this->string('description')->trim()->toString();
        $priorityCode = $this->string('priority_code')->trim()->lower()->toString();
        $categoryCode = $this->string('category_code')->trim()->lower()->toString();

        $this->merge([
            'external_user_id' => $this->string('external_user_id')->trim()->toString(),
            'external_ticket_id' => $externalTicketId !== '' ? $externalTicketId : null,
            'subject' => $this->string('subject')->trim()->toString(),
            'description' => $description !== '' ? $description : null,
            'priority_code' => $priorityCode !== '' ? $priorityCode : null,
            'category_code' => $categoryCode !== '' ? $categoryCode : null,
        ]);
    }
}
