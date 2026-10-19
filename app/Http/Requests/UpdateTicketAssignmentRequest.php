<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTicketAssignmentRequest extends FormRequest
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
            'assigned_to' => [
                'nullable',
                'uuid',
                Rule::exists(User::class, 'id')->where('is_active', true),
            ],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'assigned_to.exists' => 'کارشناس انتخاب‌شده معتبر یا فعال نیست.',
            'reason.max' => 'دلیل تخصیص نمی‌تواند بیشتر از ۱۰۰۰ کاراکتر باشد.',
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $ticket = $this->route('ticket');
                $assignedTo = $this->input('assigned_to');

                if ($ticket instanceof Ticket && $ticket->assigned_to === $assignedTo) {
                    $validator->errors()->add('assigned_to', 'کارشناس جدید باید با مسئول فعلی متفاوت باشد.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $assignedTo = $this->string('assigned_to')->trim()->toString();
        $reason = $this->string('reason')->trim()->toString();

        $this->merge([
            'assigned_to' => $assignedTo !== '' ? $assignedTo : null,
            'reason' => $reason !== '' ? $reason : null,
        ]);
    }
}
