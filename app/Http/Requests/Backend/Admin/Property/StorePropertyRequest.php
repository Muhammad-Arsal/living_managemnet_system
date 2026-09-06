<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Enums\RentalMode;
use App\Http\Requests\Concerns\ValidatesDocuments;
use App\Http\Requests\Concerns\ValidatesUkAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePropertyRequest extends FormRequest
{
    use ValidatesDocuments;
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
            'rental_mode' => ['required', Rule::enum(RentalMode::class)],
            'rooms' => ['nullable', 'array'],
            'rooms.*.name' => ['required_with:rooms', 'string', 'max:100'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:'.$mimes, 'max:'.$maxKb],
        ], $this->ukAddressRules(), $this->documentRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $mode = $this->input('rental_mode');

            if ($mode === RentalMode::PerRoom->value) {
                $rooms = $this->input('rooms', []);

                if (! is_array($rooms) || count($rooms) < 1) {
                    $validator->errors()->add('rooms', 'Add at least one room for per-room rental.');
                } else {
                    $names = collect($rooms)->pluck('name')->map(fn ($n) => is_string($n) ? mb_strtolower(trim($n)) : '');
                    if ($names->filter()->unique()->count() !== $names->filter()->count()) {
                        $validator->errors()->add('rooms', 'Room names must be unique on this property.');
                    }
                }
            }

            if ($mode === RentalMode::Whole->value && filled($this->input('rooms'))) {
                $validator->errors()->add('rooms', 'Rooms cannot be added when renting as a Whole Property.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->ukAddressMessages();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return array_merge($this->documentAttributes(), [
            'rooms.*.name' => 'room name',
            'rental_mode' => 'rental mode',
        ]);
    }

    protected function prepareForValidation(): void
    {
        $this->mergeNormalisedAddress();
    }
}
