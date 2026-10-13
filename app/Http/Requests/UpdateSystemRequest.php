<?php

namespace App\Http\Requests;

use App\Models\System;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $system = $this->route('system');

        return $system instanceof System && ($this->user()?->can('update', $system) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'نام سامانه را وارد کنید.',
            'name.max' => 'نام سامانه نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
            'description.max' => 'توضیحات نمی‌تواند بیشتر از ۲۰۰۰ کاراکتر باشد.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $description = $this->string('description')->trim()->toString();

        $this->merge([
            'name' => $this->string('name')->trim()->toString(),
            'description' => $description !== '' ? $description : null,
        ]);
    }
}
