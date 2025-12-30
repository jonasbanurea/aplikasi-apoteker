<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'batch_id' => ['nullable', 'exists:stock_batches,id'],
            'type' => ['required', Rule::in(['IN', 'OUT', 'ADJUST'])],
            'qty' => ['required', 'integer', 'not_in:0'],
            'ref_type' => ['nullable', 'string', 'max:100'],
            'ref_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
