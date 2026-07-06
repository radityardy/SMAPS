<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        if ($this->user()) {
            $this->merge([
                'patient_name' => $this->input('patient_name') ?? $this->user()->name,
                'patient_phone' => $this->input('patient_phone') ?? $this->user()->phone ?? null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:doctors,id'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['nullable', 'string', 'max:20'],
            'complaint' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
