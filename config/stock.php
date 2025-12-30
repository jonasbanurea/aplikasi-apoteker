<?php

return [
    // Nilai rupiah selisih stok (qty * cost) yang membutuhkan persetujuan owner sebelum penyesuaian dilakukan
    'adjustment_owner_threshold' => env('STOCK_ADJUSTMENT_OWNER_THRESHOLD', 1000000),

    // Batas hari untuk near-expired; urutan pertama jadi default jika query tidak diisi
    'near_expired_days' => [90, 60, 30],
    'default_near_expired_days' => env('STOCK_NEAR_EXPIRED_DAYS', 90),

    // Horizon penjualan untuk referensi reorder (dalam hari)
    'reorder_sales_lookback_days' => 30,
];
