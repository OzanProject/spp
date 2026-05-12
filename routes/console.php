<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\InvoiceService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── SPP Scheduler ────────────────────────────────────────────────────────────

// 1. Generate tagihan bulanan → setiap tanggal 1 jam 07:00
Schedule::command('spp:generate-invoices')
    ->monthlyOn(1, '07:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 2. Tandai overdue → setiap hari jam 00:05 (setelah midnight)
Schedule::command('spp:mark-overdue')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 3. Kirim reminder WA H-3 → setiap hari jam 08:00
Schedule::command('spp:send-reminders --days=3')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));

// 4. Kirim reminder WA H-1 → setiap hari jam 08:30
Schedule::command('spp:send-reminders --days=1')
    ->dailyAt('08:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
