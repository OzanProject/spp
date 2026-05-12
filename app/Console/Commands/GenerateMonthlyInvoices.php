<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature   = 'spp:generate-invoices {--month= : Bulan (1-12)} {--year= : Tahun}';
    protected $description = 'Generate tagihan SPP bulanan untuk semua siswa';

    public function handle(): int
    {
        $month = (int) ($this->option('month') ?? now()->month);
        $year  = (int) ($this->option('year')  ?? now()->year);

        $this->info("Generating invoices untuk {$month}/{$year}...");

        $result = InvoiceService::generateMonthly($month, $year);

        $this->info("✅ Dibuat : {$result['created']} tagihan");
        $this->info("⏭️  Dilewati: {$result['skipped']} tagihan (sudah ada)");

        return self::SUCCESS;
    }
}
