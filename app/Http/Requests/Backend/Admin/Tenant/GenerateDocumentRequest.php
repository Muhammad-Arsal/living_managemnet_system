<?php

namespace App\Http\Requests\Backend\Admin\Tenant;

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

    protected function prepareForValidation(): void
    {
        $template = is_string($this->input('template'))
            ? DocumentTemplateKey::tryFrom((string) $this->input('template'))
            : null;

        if ($template?->isStatic() && ! $this->has('fields')) {
            $this->merge(['fields' => []]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $templateValue = $this->input('template');
        $template = is_string($templateValue)
            ? DocumentTemplateKey::tryFrom($templateValue)
            : null;

        $rules = [
            'template' => ['required', Rule::enum(DocumentTemplateKey::class)],
            'tenancy_id' => ['nullable', 'integer', 'exists:tenancies,id'],
            'fields' => [$template?->isStatic() ? 'nullable' : 'required', 'array'],
        ];

        if ($template === null || $template->isStatic()) {
            return $rules;
        }

        /** @var DocumentGenerationService $generation */
        $generation = app(DocumentGenerationService::class);

        foreach ($generation->fieldDefinitions($template) as $key => $definition) {
            $type = (string) ($definition['type'] ?? 'text');
            $required = (bool) ($definition['required'] ?? false);

            if ($type === 'checkboxes') {
                $rules['fields.'.$key] = [$required ? 'required' : 'nullable', 'array'];
                $optionKeys = array_map('strval', array_keys($definition['options'] ?? []));
                $optionValues = array_map('strval', array_values($definition['options'] ?? []));
                $allowed = array_values(array_unique(array_merge($optionKeys, $optionValues)));
                $rules['fields.'.$key.'.*'] = ['string', Rule::in($allowed)];

                continue;
            }

            if ($type === 'score_row') {
                $rules['fields.'.$key] = [$required ? 'required' : 'nullable', 'array'];
                $rules['fields.'.$key.'.*'] = ['nullable', 'string', 'max:255'];

                continue;
            }

            $fieldRules = [$required ? 'required' : 'nullable', 'string'];

            if ($type === 'textarea') {
                $fieldRules[] = 'max:10000';
            } elseif (in_array($type, ['select', 'radio'], true) && ! empty($definition['options'])) {
                $fieldRules[] = Rule::in(array_map('strval', array_keys($definition['options'])));
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
            'template' => 'document template',
            'tenancy_id' => 'property context',
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
