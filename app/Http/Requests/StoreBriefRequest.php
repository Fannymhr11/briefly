<?php

namespace App\Http\Requests;

use App\Models\Brief;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBriefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('user') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:'.Brief::MAX_FILE_KB],
            'platform' => ['required', Rule::in(array_keys(Brief::PLATFORMS))],
            'brand' => ['required', Rule::in(array_keys(Brief::BRANDS))],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File brief PDF wajib diunggah.',
            'file.file' => 'File brief tidak valid.',
            'file.mimes' => 'File brief harus berformat PDF.',
            'file.mimetypes' => 'File brief harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.uploaded' => 'File gagal diunggah. Pastikan ukuran maksimal 10 MB.',
            'platform.required' => 'Platform wajib dipilih.',
            'platform.in' => 'Platform tidak valid.',
            'brand.required' => 'Brand wajib dipilih.',
            'brand.in' => 'Brand tidak valid.',
        ];
    }
}
