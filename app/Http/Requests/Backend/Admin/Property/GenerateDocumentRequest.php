<?php

namespace App\Http\Requests\Backend\Admin\Property;

use App\Enums\DocumentTemplateKey;
use App\Services\Admin\DocumentGenerationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateDocumentRequest extends FormRequest
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
        $rules = [
            'template' => ['required', Rule::enum(DocumentTemplateKey::class)],
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'fields' => ['required', 'array'],
        ];

        $templateValue = $this->input('template');
        $template = is_string($templateValue)
            ? DocumentTemplateKey::tryFrom($templateValue)
            : null;

        if ($template === null) {
            return $rules;
        }

        /** @var DocumentGenerationService $generation */
        $generation = app(DocumentGenerationService::class);

        foreach ($generation->fieldDefinitions($template) as $key => $definition) {
            $fieldRules = [(bool) ($definition['required'] ?? false) ? 'required' : 'nullable', 'string'];

            if (($definition['type'] ?? 'text') === 'textarea') {
                $fieldRules[] = 'max:10000';
            } else {
                $fieldRules[] = 'max:500';
            }

            $rules['fields.'.$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'tenant_id' => 'resident',
            'template' => 'document template',
        ];

        $templateValue = $this->input('template');
        $template = is_string($templateValue)
            ? DocumentTemplateKey::tryFrom($templateValue)
            : null;

        if ($template === null) {
            return $attributes;
        }

        /** @var DocumentGenerationService $generation */
        $generation = app(DocumentGenerationService::class);

        foreach ($generation->fieldDefinitions($template) as $key => $definition) {
            $attributes['fields.'.$key] = (string) $definition['label'];
        }

        return $attributes;
    }
}
