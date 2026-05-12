<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cleanup unwanted keys
        DB::table('settings')->where('key', 'midtrans_merchant_id')->delete();

        $settings = [
            [
                'key'   => 'bank_name',
                'group' => 'bank',
                'label' => 'Nama Bank',
                'value' => '',
            ],
            [
                'key'   => 'bank_account',
                'group' => 'bank',
                'label' => 'Nomor Rekening',
                'value' => '',
            ],
            [
                'key'   => 'bank_holder',
                'group' => 'bank',
                'label' => 'Atas Nama',
                'value' => '',
            ],
            [
                'key'   => 'midtrans_active',
                'group' => 'bank',
                'label' => 'Aktifkan Payment Gateway',
                'value' => '0',
            ],
            [
                'key'   => 'payment_gateway_provider',
                'group' => 'bank',
                'label' => 'Pilih Provider Payment Gateway',
                'value' => 'none',
            ],
            [
                'key'   => 'midtrans_server_key',
                'group' => 'bank',
                'label' => 'Midtrans Server Key',
                'value' => '',
            ],
            [
                'key'   => 'midtrans_client_key',
                'group' => 'bank',
                'label' => 'Midtrans Client Key',
                'value' => '',
            ],
            [
                'key'   => 'midtrans_is_production',
                'group' => 'bank',
                'label' => 'Mode Production Midtrans',
                'value' => '0',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'midtrans_active',
            'payment_gateway_provider',
            'midtrans_server_key',
            'midtrans_client_key',
            'midtrans_is_production',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
