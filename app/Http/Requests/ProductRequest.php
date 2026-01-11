<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'nama_dagang' => ['required', 'string', 'max:150'],
            'nama_generik' => ['nullable', 'string', 'max:150'],
            'bentuk' => ['required', 'string', 'max:100'],
            'kekuatan_dosis' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:50'],
            'golongan' => ['required', Rule::in(['OTC', 'BEBAS_TERBATAS', 'RESEP', 'PSIKOTROPIKA', 'NARKOTIKA'])],
            'wajib_resep' => ['sometimes', 'boolean'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'lokasi_rak' => ['nullable', 'string', 'max:100'],
            'minimal_stok' => ['required', 'integer', 'min:0'],
            'konsinyasi' => ['sometimes', 'boolean'],
            
            // Field untuk penjualan eceran
            'jual_eceran' => ['sometimes', 'boolean'],
            'unit_kemasan' => ['nullable', 'required_if:jual_eceran,1', 'string', 'max:50'],
            'unit_terkecil' => ['nullable', 'required_if:jual_eceran,1', 'string', 'max:50'],
            'isi_per_kemasan' => ['nullable', 'required_if:jual_eceran,1', 'integer', 'min:1'],
        ];
    }
}
