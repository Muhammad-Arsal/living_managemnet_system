<?php

namespace App\Http\Requests\Backend\Admin\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class AssignPropertyRequest extends FormRequest
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
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'started_on' => ['required', 'date'],
        ];
    }
}
