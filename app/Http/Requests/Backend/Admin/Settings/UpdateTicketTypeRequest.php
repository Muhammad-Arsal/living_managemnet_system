<?php

namespace App\Http\Requests\Backend\Admin\Settings;

use App\Models\TicketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketTypeRequest extends FormRequest
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
        /** @var TicketType $ticketType */
        $ticketType = $this->route('ticket_type');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ticket_types', 'name')->ignore($ticketType),
            ],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
