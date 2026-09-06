<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Enums\RentalMode;
use App\Http\Requests\Concerns\ValidatesUkAddress;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'rental_mode' => ['required', Rule::enum(RentalMode::class)],
            'rooms' => ['nullable', 'array'],
            'rooms.*.name' => ['required_with:rooms', 'string', 'max:100'],
        ], $this->ukAddressRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Property $property */
            $property = $this->route('property');
            $mode = $this->input('rental_mode');
            $switchingToPerRoom = $mode === RentalMode::PerRoom->value
                && $property->rental_mode !== RentalMode::PerRoom;

            if ($switchingToPerRoom) {
                $rooms = $this->input('rooms', []);

                if (! is_array($rooms) || count($rooms) < 1) {
                    $validator->errors()->add('rooms', 'Add at least one room when switching to per-room rental.');
                } else {
                    $names = collect($rooms)->pluck('name')->map(fn ($n) => is_string($n) ? mb_strtolower(trim($n)) : '');
                    if ($names->filter()->unique()->count() !== $names->filter()->count()) {
                        $validator->errors()->add('rooms', 'Room names must be unique on this property.');
                    }
                }
            }

            if ($mode === RentalMode::Whole->value
                && $property->rental_mode === RentalMode::Whole
                && filled($this->input('rooms'))) {
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
        return [
            'rooms.*.name' => 'room name',
            'rental_mode' => 'rental mode',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->mergeNormalisedAddress();
    }
}
