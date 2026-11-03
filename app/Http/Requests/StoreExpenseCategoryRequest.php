<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_tax_deductible_default' => ['nullable', 'boolean'],
            'default_tax_category' => ['nullable', 'string', 'max:100'],
            'default_deduction_percentage' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ];
    }
}
