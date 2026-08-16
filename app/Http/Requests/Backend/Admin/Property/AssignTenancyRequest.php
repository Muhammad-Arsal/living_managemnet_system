<?php

namespace App\Http\Requests\Backend\Admin\Property;

use Illuminate\Foundation\Http\FormRequest;

class AssignTenancyRequest extends FormRequest
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
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'started_on' => ['required', 'date'],
        ];
    }
}
