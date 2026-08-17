<?php

namespace App\Http\Requests\Backend\Admin\Property;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyImagesRequest extends FormRequest
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
        $maxKb = (int) config('properties.images.max_kilobytes', 5120);
        $mimes = implode(',', config('properties.images.mimes', ['jpg', 'jpeg', 'png', 'webp']));

        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:'.$mimes, 'max:'.$maxKb],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'images' => 'images',
            'images.*' => 'image',
        ];
    }
}
