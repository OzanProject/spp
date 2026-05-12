<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature   = 'spp:mark-overdue';
    protected $description = 'Tandai tagihan yang sudah lewat jatuh tempo sebagai overdue';

    public function handle(): int
    {
        $count = InvoiceService::markOverdue();

        if (setting('late_fee_active', '0') === '1') {
            $fined = InvoiceService::applyLateFee();
            $this->info("💸 Denda diterapkan pada {$fined} tagihan");
        }

        $this->info("⚠️  {$count} tagihan ditandai overdue");

        return self::SUCCESS;
    }
}
