<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgentTicketMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket && ($this->user()?->can('update', $ticket) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:20000'],
            'is_internal_note' => ['required', 'boolean'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,txt,csv,doc,docx,xls,xlsx,zip',
                'extensions:jpg,jpeg,png,webp,pdf,txt,csv,doc,docx,xls,xlsx,zip',
                'max:10240',
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'message.required' => 'متن پاسخ یا یادداشت را وارد کنید.',
            'message.max' => 'متن پیام نمی‌تواند بیشتر از ۲۰۰۰۰ کاراکتر باشد.',
            'attachments.max' => 'در هر پیام حداکثر ۵ فایل قابل ارسال است.',
            'attachments.*.mimes' => 'نوع یکی از فایل‌های پیوست مجاز نیست.',
            'attachments.*.extensions' => 'پسوند یکی از فایل‌های پیوست مجاز نیست.',
            'attachments.*.max' => 'حجم هر فایل نباید بیشتر از ۱۰ مگابایت باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => $this->string('message')->trim()->toString(),
            'is_internal_note' => $this->boolean('is_internal_note'),
        ]);
    }
}
