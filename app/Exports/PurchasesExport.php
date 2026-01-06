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

class PurchasesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
        $query = Purchase::with(['supplier', 'user', 'items.product'])
            ->orderBy('purchase_date', 'desc');

        if ($this->startDate) {
            $query->whereDate('purchase_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('purchase_date', '<=', $this->endDate);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No PO',
            'Tanggal Pembelian',
            'Supplier',
            'Status',
            'Subtotal',
            'Diskon',
            'PPN',
            'Total',
            'Dibuat Oleh',
            'Catatan',
            'Jumlah Item',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->po_number,
            $purchase->purchase_date->format('Y-m-d'),
            $purchase->supplier->nama ?? '-',
            $purchase->status,
            $purchase->subtotal,
            $purchase->discount_amount,
            $purchase->tax_amount,
            $purchase->total_amount,
            $purchase->user->name ?? '-',
            $purchase->notes ?? '-',
            $purchase->items->count(),
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
            'A' => 20, // No PO
            'B' => 18, // Tanggal
            'C' => 25, // Supplier
            'D' => 15, // Status
            'E' => 15, // Subtotal
            'F' => 12, // Diskon
            'G' => 12, // PPN
            'H' => 15, // Total
            'I' => 20, // Dibuat Oleh
            'J' => 30, // Catatan
            'K' => 12, // Jumlah Item
        ];
    }
}
