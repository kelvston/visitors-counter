<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // Shop Info
            ['key' => 'shop_name', 'value' => 'My Retail Shop', 'group' => 'shop'],
            ['key' => 'shop_address', 'value' => 'Dar es Salaam, Tanzania', 'group' => 'shop'],
            ['key' => 'contact_phone', 'value' => '+255700000000', 'group' => 'shop'],
            ['key' => 'contact_email', 'value' => 'info@example.com', 'group' => 'shop'],
            ['key' => 'currency_symbol', 'value' => 'TZS', 'group' => 'shop'],
            ['key' => 'date_format', 'value' => 'DD/MM/YYYY', 'group' => 'shop'],
            ['key' => 'time_format', 'value' => '24-hour', 'group' => 'shop'],
            ['key' => 'time_zone', 'value' => 'Africa/Dar_es_Salaam', 'group' => 'shop'],
            ['key' => 'operating_hours', 'value' => '08:00 - 20:00', 'group' => 'shop'],

            // Receipt & Invoice
            ['key' => 'receipt_footer_text', 'value' => 'Thank you for your purchase!', 'group' => 'receipt'],
            ['key' => 'print_logo', 'value' => 'on', 'group' => 'receipt'],
            ['key' => 'include_customer_details', 'value' => 'on', 'group' => 'receipt'],
            ['key' => 'receipt_number_sequence', 'value' => '1000', 'group' => 'receipt'],
            ['key' => 'email_receipts', 'value' => 'off', 'group' => 'receipt'],
            ['key' => 'invoice_template', 'value' => 'default', 'group' => 'receipt'],

            // Discounts & Promotions
            ['key' => 'allow_manual_discounts', 'value' => 'on', 'group' => 'discount'],

            // Checkout Flow
            ['key' => 'checkout_behavior', 'value' => 'print_receipt', 'group' => 'checkout'],

            // Stock Adjustments
            ['key' => 'stock_adjustment_reasons', 'value' => 'damage,theft,donation,internal use', 'group' => 'stock'],
            ['key' => 'allow_stock_adjustments', 'value' => 'on', 'group' => 'stock'],
            ['key' => 'audit_stock_changes', 'value' => 'on', 'group' => 'stock'],

            // Product & Category
            ['key' => 'default_unit', 'value' => 'pcs', 'group' => 'product'],

            // Stock Alerts
            ['key' => 'default_low_stock_threshold', 'value' => '10', 'group' => 'alerts'],

            // Employee Management
            ['key' => 'role_assignment_enabled', 'value' => 'on', 'group' => 'employee'],

            // Backup
            ['key' => 'enable_daily_backup', 'value' => 'off', 'group' => 'backup'],
            ['key' => 'backup_time', 'value' => '23:00', 'group' => 'backup'],
            ['key' => 'backup_retention_days', 'value' => '7', 'group' => 'backup'],
            ['key' => 'backup_destination', 'value' => 'local', 'group' => 'backup'],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'], 'group' => $item['group']]
            );
        }
    }
}

