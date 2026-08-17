<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Models\Property;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class EndTenancyRequest extends FormRequest
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
        $startDate = $this->tenancyStartDate();
        $rules = ['required', 'date'];

        if ($startDate !== null) {
            $rules[] = 'after_or_equal:'.$startDate;

            if ($startDate <= now()->toDateString()) {
                $rules[] = 'before_or_equal:today';
            }
        }

        return [
            'ended_on' => $rules,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $startDate = $this->tenancyStartDate();
        $formatted = $startDate ? Carbon::parse($startDate)->format('d M Y') : null;

        return [
            'ended_on.after_or_equal' => $formatted
                ? 'The end date must be on or after the tenancy start date ('.$formatted.').'
                : 'The end date must be on or after the tenancy start date.',
        ];
    }

    private function tenancyStartDate(): ?string
    {
        $property = $this->route('property');
        if ($property instanceof Property) {
            $property->loadMissing('currentTenancy');

            return $property->currentTenancy?->started_on?->toDateString();
        }

        $tenant = $this->route('tenant');
        if ($tenant instanceof Tenant) {
            $tenant->loadMissing('currentTenancy');

            return $tenant->currentTenancy?->started_on?->toDateString();
        }

        return null;
    }
}
