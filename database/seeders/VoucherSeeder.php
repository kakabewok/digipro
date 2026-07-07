<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'HEMAT10',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 20000,
                'max_usage' => 100,
                'used_count' => 0,
                'expired_at' => now()->addMonths(3),
            ],
            [
                'code' => 'DISKON20K',
                'type' => 'nominal',
                'value' => 20000,
                'min_purchase' => 50000,
                'max_usage' => 50,
                'used_count' => 0,
                'expired_at' => now()->addMonths(3),
            ],
            [
                'code' => 'NEWUSER',
                'type' => 'percentage',
                'value' => 15,
                'min_purchase' => 0,
                'max_usage' => 200,
                'used_count' => 0,
                'expired_at' => now()->addMonths(6),
            ],
            [
                'code' => 'RESELLER5',
                'type' => 'percentage',
                'value' => 5,
                'min_purchase' => 100000,
                'max_usage' => 500,
                'used_count' => 0,
                'expired_at' => now()->addYear(),
            ],
            [
                'code' => 'FLASH50K',
                'type' => 'nominal',
                'value' => 50000,
                'min_purchase' => 150000,
                'max_usage' => 10,
                'used_count' => 0,
                'expired_at' => now()->addMonth(),
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
