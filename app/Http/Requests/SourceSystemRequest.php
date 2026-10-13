<?php

namespace App\Http\Requests;

use App\Models\System;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

abstract class SourceSystemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->attributes->get('sourceSystem') instanceof System;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    public function sourceSystem(): System
    {
        $sourceSystem = $this->attributes->get('sourceSystem');

        abort_unless($sourceSystem instanceof System, 401, 'کلید API معتبر نیست.');

        return $sourceSystem;
    }
}
