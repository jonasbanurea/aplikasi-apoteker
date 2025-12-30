<?php

namespace App\Services;

use App\Models\StockBatch;
use Carbon\Carbon;
use RuntimeException;

class FefoAllocatorService
{
    /**
     * Allocate quantities per item using FEFO (earliest expired first, non-expired only).
     * @param array<int, array{product_id:int, qty:int, product_name?:string}> $items
     * @return array<int, array<int, array{batch:\App\Models\StockBatch, qty:int}>>
     */
    public function allocate(array $items): array
    {
        $allocations = [];

        foreach ($items as $index => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $qtyNeeded = (int) ($item['qty'] ?? 0);
            $productName = $item['product_name'] ?? '';

            $allocations[$index] = $this->allocateItem($productId, $qtyNeeded, $productName);
        }

        return $allocations;
    }

    /**
     * @return array<int, array{batch:StockBatch, qty:int}>
     */
    protected function allocateItem(int $productId, int $qtyNeeded, string $productName = ''): array
    {
        if ($qtyNeeded <= 0) {
            throw new RuntimeException('Qty penjualan harus lebih besar dari 0.');
        }

        $today = Carbon::today();

        $batches = StockBatch::where('product_id', $productId)
            ->where(function ($q) use ($today) {
                $q->whereNull('expired_date')
                  ->orWhere('expired_date', '>=', $today);
            })
            ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expired_date')
            ->lockForUpdate()
            ->get();

        $available = $batches->sum('qty_on_hand');
        if ($available < $qtyNeeded) {
            $name = $productName ?: 'produk ID ' . $productId;
            throw new RuntimeException('Stok tidak cukup untuk ' . $name . '. Tersedia: ' . $available . ', diminta: ' . $qtyNeeded);
        }

        $remaining = $qtyNeeded;
        $result = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, $batch->qty_on_hand);
            if ($take > 0) {
                $result[] = [
                    'batch' => $batch,
                    'qty' => $take,
                ];
                $remaining -= $take;
            }
        }

        return $result;
    }
}
