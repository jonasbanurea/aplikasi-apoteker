<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PurchaseItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = Purchase::with(['supplier', 'items.product'])
            ->orderBy('date', 'desc');

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        $purchases = $query->get();
        
        // Flatten items untuk setiap purchase
        $flatData = collect();
        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $flatData->push([
                    'purchase' => $purchase,
                    'item' => $item
                ]);
            }
        }
        
        return $flatData;
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Supplier',
            'Status',
            'SKU',
            'Nama Produk',
            'Batch No',
            'Expired Date',
            'Qty',
            'Bonus',
            'Harga Beli',
            'Subtotal Item',
        ];
    }

    public function map($data): array
    {
        $purchase = $data['purchase'];
        $item = $data['item'];
        
        return [
            $purchase->invoice_no,
            $purchase->date->format('Y-m-d'),
            $purchase->supplier->nama ?? '-',
            $purchase->status,
            $item->product->sku ?? '-',
            $item->product->nama_dagang ?? '-',
            $item->batch_no ?? '-',
            $item->expired_date ? $item->expired_date->format('Y-m-d') : '-',
            $item->qty,
            $item->bonus_qty ?? 0,
            $item->cost_price,
            $item->qty * $item->cost_price,
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
            'B' => 15, // Tanggal
            'C' => 25, // Supplier
            'D' => 12, // Status
            'E' => 15, // SKU
            'F' => 30, // Nama Produk
            'G' => 15, // Batch No
            'H' => 15, // Expired Date
            'I' => 10, // Qty
            'J' => 10, // Bonus
            'K' => 15, // Harga Beli
            'L' => 15, // Subtotal Item
        ];
    }
}
