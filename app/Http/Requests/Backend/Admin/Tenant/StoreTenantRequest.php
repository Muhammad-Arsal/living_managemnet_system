<?php

namespace App\Http\Requests\Backend\Admin\Tenant;

use App\Http\Requests\Concerns\ValidatesUkAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    use ValidatesUkAddress;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile_number' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tenants', 'email')],
        ], $this->ukAddressRules());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->ukAddressMessages();
    }

    protected function prepareForValidation(): void
    {
        $this->mergeNormalisedAddress();
    }
}
