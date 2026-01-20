<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $productId;

    public function __construct($startDate = null, $endDate = null, $productId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->productId = $productId;
    }

    public function collection()
    {
        $query = StockMovement::with(['product', 'batch', 'user'])
            ->orderBy('created_at', 'desc');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'SKU',
            'Produk',
            'Batch',
            'Tipe',
            'Qty',
            'Qty Sebelum',
            'Qty Sesudah',
            'Reference Type',
            'Reference ID',
            'User',
            'Catatan',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->created_at->format('Y-m-d H:i:s'),
            $movement->product->sku ?? '-',
            $movement->product->nama_dagang ?? '-',
            $movement->batch->batch_no ?? '-',
            $movement->movement_type,
            $movement->quantity,
            $movement->qty_before,
            $movement->qty_after,
            $movement->reference_type ?? '-',
            $movement->reference_id ?? '-',
            $movement->user->name ?? 'System',
            $movement->notes ?? '-',
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
            'A' => 20, // Tanggal
            'B' => 15, // SKU
            'C' => 30, // Produk
            'D' => 20, // Batch
            'E' => 12, // Tipe
            'F' => 10, // Qty
            'G' => 12, // Qty Before
            'H' => 12, // Qty After
            'I' => 20, // Reference Type
            'J' => 12, // Reference ID
            'K' => 20, // User
            'L' => 35, // Catatan
        ];
    }
}
