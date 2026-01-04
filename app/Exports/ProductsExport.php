<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\StockBatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with(['stockBatches' => function($query) {
            $query->where('qty_on_hand', '>', 0);
        }])
        ->orderBy('lokasi_rak')
        ->orderBy('nama_dagang')
        ->get();
    }

    /**
     * @var Product $product
     */
    public function map($product): array
    {
        // Calculate total stock from active batches
        $totalStock = $product->stockBatches->sum('qty_on_hand');
        
        // Get earliest expiry date
        $earliestExpiry = $product->stockBatches
            ->where('expired_date', '!=', null)
            ->sortBy('expired_date')
            ->first();
        
        $expiryDate = $earliestExpiry ? $earliestExpiry->expired_date : '';
        
        // Calculate margin percentage
        $margin = 0;
        if ($product->harga_beli > 0) {
            $margin = ($product->harga_jual - $product->harga_beli) / $product->harga_beli;
        }
        
        return [
            $product->id,
            $product->nama_dagang,
            $product->satuan,
            $product->lokasi_rak ?? '-',
            $totalStock,
            $this->mapGolonganToKategori($product->golongan),
            $product->harga_beli,
            number_format($margin, 2),
            $product->harga_jual,
            $expiryDate,
        ];
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA BARANG',
            'SEDIAAN',
            'LOK BARANG',
            'STOK',
            'KATEGORI',
            'HRG BELI',
            'MARGIN',
            'HRG JUAL',
            'EXP DATE',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style for header row
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,  // NO
            'B' => 40, // NAMA BARANG
            'C' => 12, // SEDIAAN
            'D' => 12, // LOK BARANG
            'E' => 8,  // STOK
            'F' => 18, // KATEGORI
            'G' => 12, // HRG BELI
            'H' => 10, // MARGIN
            'I' => 12, // HRG JUAL
            'J' => 12, // EXP DATE
        ];
    }
    
    /**
     * Map golongan to kategori (reverse mapping)
     */
    private function mapGolonganToKategori(string $golongan): string
    {
        return match($golongan) {
            'OTC' => 'PRODUK BEBAS',
            'BEBAS_TERBATAS' => 'PRODUK BEBAS TERBATAS',
            'RESEP' => 'PRODUK KERAS',
            'PSIKOTROPIKA' => 'PRODUK PSIKOTROPIKA',
            'NARKOTIKA' => 'PRODUK NARKOTIKA',
            default => 'PRODUK BEBAS',
        };
    }
}
