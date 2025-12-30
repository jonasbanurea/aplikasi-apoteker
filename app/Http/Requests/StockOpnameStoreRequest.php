<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockOpnameStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opname_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.batch_id' => ['required', 'exists:stock_batches,id'],
            'items.*.physical_qty' => ['required', 'integer', 'min:0'],
            'items.*.reason' => ['required', Rule::in(['SELISIH_OPNAME', 'RUSAK', 'KADALUARSA', 'HILANG'])],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
