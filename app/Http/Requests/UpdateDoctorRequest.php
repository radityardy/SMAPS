<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->doctor->user_id)],
            'password' => ['sometimes', 'string', 'min:6'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'specialization' => ['sometimes', 'string', 'max:255'],
            'queue_prefix' => ['sometimes', 'string', 'max:5', Rule::unique('doctors')->ignore($this->doctor)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
