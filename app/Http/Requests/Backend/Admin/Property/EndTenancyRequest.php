<?php

namespace App\Http\Requests\Backend\Admin\Property;

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
        return [
            'ended_on' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
