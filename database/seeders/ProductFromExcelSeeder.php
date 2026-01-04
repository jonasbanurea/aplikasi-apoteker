<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductFromExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excelFile = database_path('../docs/NAMA -NAMA OBAT DI TOKO OBAT RO TUA.xlsx');
        
        if (!file_exists($excelFile)) {
            $this->command->error("Excel file not found: $excelFile");
            return;
        }

        try {
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $this->command->info("Importing {$highestRow} products from Excel...");
            
            $skuCounter = 1000; // Starting SKU
            
            // Start from row 2 (skip header)
            for ($row = 2; $row <= $highestRow; $row++) {
                $namaBarang = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
                
                // Skip empty rows
                if (empty($namaBarang)) {
                    continue;
                }
                
                $sediaan = trim($worksheet->getCell('C' . $row)->getValue() ?? 'PCS');
                $lokBarang = trim($worksheet->getCell('D' . $row)->getValue() ?? '');
                $stok = $worksheet->getCell('E' . $row)->getValue() ?? 0;
                $kategori = trim($worksheet->getCell('F' . $row)->getValue() ?? 'PRODUK BEBAS');
                $hrgBeli = $worksheet->getCell('G' . $row)->getValue() ?? 0;
                $margin = $worksheet->getCell('H' . $row)->getValue() ?? 0;
                $hrgJual = $worksheet->getCell('I' . $row)->getValue() ?? 0;
                $expDate = $worksheet->getCell('J' . $row)->getValue();
                
                // Map kategori to golongan
                $golongan = $this->mapKategoriToGolongan($kategori);
                
                // Map sediaan to bentuk
                $bentuk = $this->mapSediaanToBentuk($sediaan);
                
                // Generate SKU
                $sku = 'OBT' . str_pad($skuCounter++, 5, '0', STR_PAD_LEFT);
                
                // Check if product already exists
                $existingProduct = Product::where('nama_dagang', $namaBarang)->first();
                
                if ($existingProduct) {
                    $this->command->warn("Product already exists: {$namaBarang}");
                    continue;
                }
                
                // Create product
                $product = Product::create([
                    'sku' => $sku,
                    'nama_dagang' => $namaBarang,
                    'nama_generik' => $namaBarang, // Use nama_dagang as default
                    'bentuk' => $bentuk,
                    'kekuatan_dosis' => '-', // Not in Excel
                    'satuan' => $sediaan,
                    'golongan' => $golongan,
                    'wajib_resep' => in_array($golongan, ['PSIKOTROPIKA', 'NARKOTIKA']),
                    'harga_beli' => (float) $hrgBeli,
                    'harga_jual' => (float) $hrgJual,
                    'lokasi_rak' => $lokBarang,
                    'minimal_stok' => max(1, (int)$stok / 2), // Set minimal stok to half of current stock
                    'konsinyasi' => false,
                ]);
                
                // Create initial stock batch if stock > 0
                if ($stok > 0) {
                    // Convert Excel date to PHP date if needed
                    $expiryDate = null;
                    if ($expDate && is_numeric($expDate)) {
                        try {
                            $expiryDate = Date::excelToDateTimeObject($expDate)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $expiryDate = now()->addYears(2)->format('Y-m-d');
                        }
                    } else {
                        $expiryDate = now()->addYears(2)->format('Y-m-d');
                    }
                    
                    $batch = $product->stockBatches()->create([
                        'batch_no' => 'INIT-' . date('Ymd') . '-' . $product->id,
                        'qty_on_hand' => (int) $stok,
                        'cost_price' => (float) $hrgBeli,
                        'expired_date' => $expiryDate,
                        'received_at' => now(),
                    ]);
                    
                    // Create stock movement record
                    $product->stockMovements()->create([
                        'type' => 'IN',
                        'batch_id' => $batch->id,
                        'qty' => (int) $stok,
                        'ref_type' => 'initial_stock',
                        'ref_id' => null,
                        'user_id' => 1, // Owner user
                        'notes' => 'Stok awal dari data Excel - Batch: INIT-' . date('Ymd') . '-' . $product->id,
                    ]);
                }
                
                $this->command->info("Imported: {$namaBarang} (SKU: {$sku}, Stock: {$stok})");
            }
            
            $this->command->info("Product import completed successfully!");
            
        } catch (\Exception $e) {
            $this->command->error("Error importing products: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
    
    /**
     * Map kategori from Excel to golongan
     */
    private function mapKategoriToGolongan(string $kategori): string
    {
        $kategori = strtoupper($kategori);
        
        if (str_contains($kategori, 'BEBAS')) {
            return 'OTC';
        } elseif (str_contains($kategori, 'TERBATAS')) {
            return 'BEBAS_TERBATAS';
        } elseif (str_contains($kategori, 'PSIKOTROPIKA')) {
            return 'PSIKOTROPIKA';
        } elseif (str_contains($kategori, 'NARKOTIKA')) {
            return 'NARKOTIKA';
        } elseif (str_contains($kategori, 'RESEP') || str_contains($kategori, 'KERAS')) {
            return 'RESEP';
        }
        
        // Default
        return 'OTC';
    }
    
    /**
     * Map sediaan from Excel to bentuk
     */
    private function mapSediaanToBentuk(string $sediaan): string
    {
        $sediaan = strtoupper($sediaan);
        
        if (in_array($sediaan, ['TAB', 'TABLET', 'KAPLET'])) {
            return 'TABLET';
        } elseif (in_array($sediaan, ['KAPSUL', 'CAPS', 'KPS'])) {
            return 'KAPSUL';
        } elseif (in_array($sediaan, ['SIRUP', 'SYR', 'SYRUP'])) {
            return 'SIRUP';
        } elseif (in_array($sediaan, ['SALEP', 'CREAM', 'GEL'])) {
            return 'SALEP/KRIM';
        } elseif (in_array($sediaan, ['BOTOL', 'BTL'])) {
            return 'CAIRAN';
        } elseif (in_array($sediaan, ['TUBE', 'TUB'])) {
            return 'SALEP/KRIM';
        } elseif (in_array($sediaan, ['SASET', 'SACHET'])) {
            return 'SERBUK';
        } elseif (in_array($sediaan, ['BTG', 'BATANG'])) {
            return 'BATANG';
        } elseif (in_array($sediaan, ['BKS', 'BUNGKUS', 'BOX'])) {
            return 'BOX/PACK';
        }
        
        // Default return sediaan as is
        return $sediaan;
    }
}
