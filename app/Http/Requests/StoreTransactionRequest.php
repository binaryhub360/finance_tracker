<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];

        $type = $this->input('type');

        if ($type === 'income') {
            $rules['account_id'] = ['required', 'exists:accounts,id'];
            $rules['income_category_id'] = ['required', 'exists:income_categories,id'];
        } elseif ($type === 'expense') {
            $rules['account_id'] = ['required', 'exists:accounts,id'];
            $rules['expense_category_id'] = ['required', 'exists:expense_categories,id'];
        } elseif ($type === 'transfer') {
            $rules['from_account_id'] = ['required', 'exists:accounts,id', 'different:to_account_id'];
            $rules['to_account_id'] = ['required', 'exists:accounts,id', 'different:from_account_id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'amount.gt' => 'The amount must be greater than zero.',
            'from_account_id.different' => 'Source and destination accounts must be different.',
            'to_account_id.different' => 'Source and destination accounts must be different.',
        ];
    }
}
