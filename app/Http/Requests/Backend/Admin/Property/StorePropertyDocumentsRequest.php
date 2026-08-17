<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Http\Requests\Concerns\ValidatesDocuments;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyDocumentsRequest extends FormRequest
{
    use ValidatesDocuments;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->documentRules(required: true);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return $this->documentAttributes();
    }
}
