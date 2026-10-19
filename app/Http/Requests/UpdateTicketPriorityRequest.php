<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\TicketPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTicketPriorityRequest extends FormRequest
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
            'priority_id' => [
                'required',
                'uuid',
                Rule::exists(TicketPriority::class, 'id')->where('is_active', true),
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'priority_id.required' => 'اولویت جدید را انتخاب کنید.',
            'priority_id.exists' => 'اولویت انتخاب‌شده معتبر یا فعال نیست.',
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $ticket = $this->route('ticket');

                if ($ticket instanceof Ticket && $ticket->priority_id === $this->input('priority_id')) {
                    $validator->errors()->add('priority_id', 'اولویت جدید باید با اولویت فعلی متفاوت باشد.');
                }
            },
        ];
    }
}
