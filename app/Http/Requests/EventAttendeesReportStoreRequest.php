<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventAttendeesReportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\w,\s-]+\.(csv)$/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file_name.regex' => 'The name must end with .csv and contain only valid filename characters.',
        ];
    }
}