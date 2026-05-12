<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class SendInvoiceReminders extends Command
{
    protected $signature   = 'spp:send-reminders {--days=3 : Hari sebelum jatuh tempo}';
    protected $description = 'Kirim reminder WhatsApp untuk tagihan yang akan jatuh tempo';

    public function handle(): int
    {
        if (setting('wa_active', '0') !== '1') {
            $this->warn('WhatsApp gateway tidak aktif. Aktifkan di Pengaturan → Notifikasi.');
            return self::SUCCESS;
        }

        $days = (int) $this->option('days');
        $this->info("Mengirim reminder untuk tagihan jatuh tempo {$days} hari lagi...");

        $sent = InvoiceService::sendReminders($days);

        $this->info("✅ {$sent} pesan WA berhasil dikirim");

        return self::SUCCESS;
    }
}
