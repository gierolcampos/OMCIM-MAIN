<?php

namespace Database\Seeders;

use App\Models\PaymentFee;
use Illuminate\Database\Seeder;

class PaymentFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the payment fees
        $paymentFees = [
            [
                'purpose' => 'Membership Fee',
                'description' => 'Annual ICS membership fee',
                'total_price' => 200.00,
                'is_active' => true,
            ],
            [
                'purpose' => 'Event Fees',
                'description' => 'Standard event participation fee',
                'total_price' => 150.00,
                'is_active' => true,
            ],
            [
                'purpose' => 'ICS Merch',
                'description' => 'Standard merchandise price',
                'total_price' => 350.00,
                'is_active' => true,
            ],
            [
                'purpose' => 'Other',
                'description' => 'Other payment purposes',
                'total_price' => 0.00, // Default to 0 for manual entry
                'is_active' => true,
            ],
        ];

        // Insert the payment fees
        foreach ($paymentFees as $fee) {
            PaymentFee::updateOrCreate(
                ['purpose' => $fee['purpose']],
                $fee
            );
        }
    }
}
