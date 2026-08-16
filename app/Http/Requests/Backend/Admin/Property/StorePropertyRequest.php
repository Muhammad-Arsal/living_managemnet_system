<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Http\Requests\Concerns\ValidatesUkAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
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
        $maxKb = (int) config('properties.images.max_kilobytes', 5120);
        $mimes = implode(',', config('properties.images.mimes', ['jpg', 'jpeg', 'png', 'webp']));

        return array_merge([
            'name' => ['required', 'string', 'max:150'],
            'property_type_id' => ['required', 'integer', Rule::exists('property_types', 'id')->where('is_active', true)],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:'.$mimes, 'max:'.$maxKb],
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
