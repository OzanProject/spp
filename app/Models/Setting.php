<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'group', 'label', 'value'];

    // ── Groups definition ──────────────────────────────────────
    const GROUPS = [
        'school'       => 'Identitas Sekolah',
        'finance'      => 'Keuangan & SPP',
        'bank'         => 'Rekening Pembayaran',
        'notification' => 'Notifikasi',
        'appearance'   => 'Tampilan',
    ];

    // ── Default settings seeded on install ─────────────────────
    const DEFAULTS = [
        // Group: school
        ['key' => 'school_name',    'group' => 'school',   'label' => 'Nama Sekolah',        'value' => 'SMA Negeri 1 Contoh'],
        ['key' => 'school_address', 'group' => 'school',   'label' => 'Alamat',               'value' => 'Jl. Pendidikan No. 1, Kota Contoh'],
        ['key' => 'school_phone',   'group' => 'school',   'label' => 'No. Telepon',          'value' => '0211234567'],
        ['key' => 'school_email',   'group' => 'school',   'label' => 'Email Sekolah',        'value' => 'info@sekolah.sch.id'],
        ['key' => 'school_website', 'group' => 'school',   'label' => 'Website',              'value' => ''],
        ['key' => 'school_logo',    'group' => 'school',   'label' => 'Logo (nama file)',     'value' => ''],
        ['key' => 'timezone',       'group' => 'school',   'label' => 'Zona Waktu',           'value' => 'Asia/Jakarta'],
        // Group: finance
        ['key' => 'academic_year',  'group' => 'finance',  'label' => 'Tahun Ajaran Aktif',  'value' => '2025/2026'],
        ['key' => 'spp_amount',     'group' => 'finance',  'label' => 'Nominal SPP Default', 'value' => '350000'],
        ['key' => 'due_date_day',   'group' => 'finance',  'label' => 'Jatuh Tempo (tgl)',   'value' => '10'],
        ['key' => 'late_fee_active','group' => 'finance',  'label' => 'Denda Aktif (1/0)',   'value' => '0'],
        ['key' => 'late_fee_amount','group' => 'finance',  'label' => 'Nominal Denda (Rp)',  'value' => '5000'],
        ['key' => 'invoice_prefix', 'group' => 'finance',  'label' => 'Prefix Nomor Invoice','value' => 'INV'],
        // Group: bank
        ['key' => 'bank_name',                 'group' => 'bank',     'label' => 'Nama Bank',                          'value' => ''],
        ['key' => 'bank_account',              'group' => 'bank',     'label' => 'Nomor Rekening',                     'value' => ''],
        ['key' => 'bank_holder',               'group' => 'bank',     'label' => 'Atas Nama',                          'value' => ''],
        ['key' => 'midtrans_active',           'group' => 'bank',     'label' => 'Aktifkan Payment Gateway',           'value' => '0'],
        ['key' => 'payment_gateway_provider',  'group' => 'bank',     'label' => 'Pilih Provider Payment Gateway',     'value' => 'none'],
        ['key' => 'midtrans_merchant_id',      'group' => 'bank',     'label' => 'Midtrans Merchant ID',               'value' => ''],
        ['key' => 'midtrans_server_key',       'group' => 'bank',     'label' => 'Midtrans Server Key',                'value' => ''],
        ['key' => 'midtrans_client_key',       'group' => 'bank',     'label' => 'Midtrans Client Key',                'value' => ''],
        ['key' => 'midtrans_is_production',    'group' => 'bank',     'label' => 'Mode Production Midtrans',           'value' => '0'],
        // Group: notification
        ['key' => 'wa_token',       'group' => 'notification','label' => 'Token WhatsApp (Fonnte)','value' => ''],
        ['key' => 'wa_sender',      'group' => 'notification','label' => 'Nomor Pengirim WA', 'value' => ''],
        ['key' => 'wa_active',      'group' => 'notification','label' => 'WA Aktif (1/0)',    'value' => '0'],
        // Group: appearance
        ['key' => 'primary_color',  'group' => 'appearance','label' => 'Warna Utama',        'value' => '#6366f1'],
        ['key' => 'favicon',        'group' => 'appearance','label' => 'Favicon (nama file)', 'value' => ''],
    ];

    // ── Static helpers ─────────────────────────────────────────

    public static function get(string $key, $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    public static function set(string $key, $value, string $group = 'school', string $label = ''): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'label' => $label]
        );

        // Clear cache so next call reads fresh value
        Cache::forget("setting_{$key}");
    }

    public static function seedDefaults(): void
    {
        foreach (static::DEFAULTS as $item) {
            static::firstOrCreate(
                ['key' => $item['key']],
                ['group' => $item['group'], 'label' => $item['label'], 'value' => $item['value']]
            );
        }
    }
}
