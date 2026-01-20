<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Sale::with(['user', 'shift', 'items.product'])
            ->orderBy('sale_date', 'desc');

        if ($this->startDate) {
            $query->whereDate('sale_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('sale_date', '<=', $this->endDate);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Kasir',
            'Shift',
            'Customer',
            'Payment Method',
            'Subtotal',
            'Diskon',
            'PPN',
            'Total',
            'Bayar',
            'Kembalian',
            'Status',
            'Jumlah Item',
            'Produk Terjual',
        ];
    }

    public function map($sale): array
    {
        // Ambil nama produk dari items
        $productNames = $sale->items->map(function ($item) {
            $productName = $item->product->nama_dagang ?? '-';
            $qty = $item->qty;
            return "{$productName} (x{$qty})";
        })->join(', ');

        return [
            $sale->invoice_no,
            $sale->sale_date->format('Y-m-d H:i:s'),
            $sale->user->name ?? '-',
            $sale->shift ? "Shift #{$sale->shift->id}" : '-',
            $sale->customer_name ?? 'Umum',
            $sale->payment_method,
            $sale->subtotal,
            $sale->discount_amount,
            $sale->tax_amount,
            $sale->total_amount,
            $sale->paid_amount,
            $sale->change_amount,
            $sale->status,
            $sale->items->count(),
            $productNames,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // No Invoice
            'B' => 20, // Tanggal
            'C' => 20, // Kasir
            'D' => 15, // Shift
            'E' => 20, // Customer
            'F' => 15, // Payment Method
            'G' => 15, // Subtotal
            'H' => 12, // Diskon
            'I' => 12, // PPN
            'J' => 15, // Total
            'K' => 15, // Bayar
            'L' => 15, // Kembalian
            'M' => 12, // Status
            'N' => 12, // Jumlah Item
            'O' => 40, // Produk Terjual
        ];
    }
}
