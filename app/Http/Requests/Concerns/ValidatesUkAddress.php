<?php

namespace App\Http\Requests\Concerns;

use App\Support\UkContactNormaliser;

trait ValidatesUkAddress
{
    /**
     * @return array<string, mixed>
     */
    protected function ukAddressRules(): array
    {
        return [
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'address_line_3' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'county' => ['nullable', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:12', 'regex:/^[A-Z]{1,2}\d[A-Z\d]? \d[A-Z]{2}$/'],
            'country' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function ukAddressMessages(): array
    {
        return [
            'postcode.regex' => 'Enter a valid UK postcode (for example SW1A 1AA).',
        ];
    }

    protected function mergeNormalisedAddress(): void
    {
        $postcode = UkContactNormaliser::postcode($this->input('postcode'));

        $this->merge([
            'postcode' => $postcode,
            'country' => $this->filled('country') ? $this->string('country')->toString() : 'United Kingdom',
        ]);
    }
}
