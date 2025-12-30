<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $batchId = $this->route('stock_batch')?->id;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'batch_no' => ['required', 'string', 'max:100', 'unique:stock_batches,batch_no,' . $batchId . ',id,product_id,' . $this->input('product_id')],
            'expired_date' => ['nullable', 'date'],
            'qty_on_hand' => ['required', 'integer', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'received_at' => ['nullable', 'date'],
        ];
    }
}
