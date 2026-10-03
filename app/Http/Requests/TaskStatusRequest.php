<?php

namespace App\Http\Requests;

use App\Models\CreativeTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('creative') ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(array_keys(CreativeTask::STATUSES))]];
    }

    public function messages(): array
    {
        return ['status.required' => 'Status wajib dipilih.', 'status.in' => 'Status tidak valid.'];
    }
}
