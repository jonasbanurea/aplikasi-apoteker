<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Update Product Price
    |--------------------------------------------------------------------------
    |
    | Saat menerima barang dengan harga beli berbeda dari master produk,
    | sistem akan otomatis update harga_beli dan harga_jual di master produk.
    |
    */
    'auto_update_price_on_purchase' => env('AUTO_UPDATE_PRICE_ON_PURCHASE', true),

    /*
    |--------------------------------------------------------------------------
    | Default Margin Percentage
    |--------------------------------------------------------------------------
    |
    | Margin keuntungan default yang akan diterapkan saat menghitung harga jual.
    | Contoh: 0.20 = 20% markup dari harga beli
    | Rumus: harga_jual = harga_beli * (1 + margin)
    |
    */
    'default_margin_percentage' => env('DEFAULT_MARGIN_PERCENTAGE', 0.20), // 20%

    /*
    |--------------------------------------------------------------------------
    | Margin by Product Category
    |--------------------------------------------------------------------------
    |
    | Margin berbeda per golongan produk.
    | Jika tidak diset, akan menggunakan default_margin_percentage
    |
    */
    'margin_by_golongan' => [
        'OTC' => 0.25,              // 25% untuk obat bebas
        'BEBAS_TERBATAS' => 0.23,   // 23% 
        'RESEP' => 0.20,            // 20% untuk obat keras
        'PSIKOTROPIKA' => 0.18,     // 18%
        'NARKOTIKA' => 0.18,        // 18%
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Update Threshold
    |--------------------------------------------------------------------------
    |
    | Hanya update harga jika selisih harga beli baru dengan lama melebihi threshold.
    | Contoh: 0.05 = update hanya jika selisih > 5%
    | Set 0 untuk selalu update meskipun hanya beda Rp 1
    |
    */
    'price_update_threshold_percentage' => env('PRICE_UPDATE_THRESHOLD', 0.02), // 2%

    /*
    |--------------------------------------------------------------------------
    | Round Selling Price
    |--------------------------------------------------------------------------
    |
    | Pembulatan harga jual ke kelipatan tertentu
    | Contoh: 100 = bulatkan ke ratusan (1.250 -> 1.300)
    |         1000 = bulatkan ke ribuan (12.800 -> 13.000)
    |         1 = tidak ada pembulatan
    |
    */
    'round_selling_price_to' => env('ROUND_SELLING_PRICE_TO', 100),
];
