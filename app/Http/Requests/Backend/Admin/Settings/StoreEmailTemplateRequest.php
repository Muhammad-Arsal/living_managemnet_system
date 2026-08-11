<?php

namespace App\Http\Requests\Backend\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email_type' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('email_templates', 'email_type'),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'html_content' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email_type')) {
            $this->merge([
                'email_type' => strtolower(str_replace(' ', '_', (string) $this->input('email_type'))),
            ]);
        }

        if (! $this->has('status')) {
            $this->merge(['status' => true]);
        } else {
            $this->merge(['status' => $this->boolean('status')]);
        }
    }
}
