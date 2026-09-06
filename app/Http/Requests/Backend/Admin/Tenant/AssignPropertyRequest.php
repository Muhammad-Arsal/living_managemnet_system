<?php

namespace App\Http\Requests\Backend\Admin\Tenant;

use App\Enums\RentalMode;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'started_on' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $propertyId = $this->integer('property_id');

            if ($propertyId < 1) {
                return;
            }

            $property = Property::query()->find($propertyId);

            if ($property === null) {
                return;
            }

            if ($property->rental_mode === RentalMode::PerRoom) {
                if (! $this->filled('room_id')) {
                    $validator->errors()->add('room_id', 'A room is required for per-room properties.');

                    return;
                }

                $roomBelongs = $property->rooms()->whereKey($this->integer('room_id'))->exists();

                if (! $roomBelongs) {
                    $validator->errors()->add('room_id', 'The selected room does not belong to this property.');
                }
            }

            if ($property->rental_mode === RentalMode::Whole && $this->filled('room_id')) {
                $validator->errors()->add('room_id', 'Rooms cannot be assigned on whole-house properties.');
            }
        });
    }
}
