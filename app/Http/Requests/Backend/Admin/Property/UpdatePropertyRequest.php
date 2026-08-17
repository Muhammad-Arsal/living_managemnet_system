<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Http\Requests\Concerns\ValidatesUkAddress;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
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
        /** @var Property $property */
        $property = $this->route('property');

        return array_merge([
            'name' => ['required', 'string', 'max:150'],
            'property_type_id' => [
                'required',
                'integer',
                Rule::exists('property_types', 'id')->where(function ($query) use ($property) {
                    $query->where('is_active', true)
                        ->orWhere('id', $property->property_type_id);
                }),
            ],
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
