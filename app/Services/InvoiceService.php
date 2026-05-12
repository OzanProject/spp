<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    // ── Generate monthly invoices for all students ─────────────
    public static function generateMonthly(int $month, int $year, ?int $amount = null, ?string $dueDate = null): array
    {
        $amount  = $amount  ?? (int) setting('spp_amount', 350000);
        $day     = (int) setting('due_date_day', 10);
        $dueDate = $dueDate ?? Carbon::create($year, $month, $day)->format('Y-m-d');

        $created = 0;
        $skipped = 0;

        foreach (Student::all() as $student) {
            $exists = Invoice::where('student_id', $student->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if (!$exists) {
                Invoice::create([
                    'student_id' => $student->id,
                    'month'      => $month,
                    'year'       => $year,
                    'amount'     => $amount,
                    'due_date'   => $dueDate,
                    'status'     => 'unpaid',
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        Log::info("[InvoiceService] Generated: {$created}, Skipped: {$skipped} — {$month}/{$year}");

        return compact('created', 'skipped');
    }

    // ── Mark overdue invoices ──────────────────────────────────
    public static function markOverdue(): int
    {
        $updated = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        Log::info("[InvoiceService] Marked overdue: {$updated}");

        return $updated;
    }

    // ── Apply late fee ─────────────────────────────────────────
    public static function applyLateFee(): int
    {
        if (setting('late_fee_active', '0') !== '1') {
            return 0;
        }

        $feeAmount = (int) setting('late_fee_amount', 5000);

        $overdueInvoices = Invoice::where('status', 'overdue')
            ->whereDoesntHave('payment')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            // Add denda if not already added (simple approach: add to amount cap)
            if (!str_contains((string)$invoice->notes, '[DENDA]')) {
                $invoice->update([
                    'amount' => $invoice->amount + $feeAmount,
                    'notes'  => trim($invoice->notes . ' [DENDA Rp ' . number_format($feeAmount, 0, ',', '.') . ']'),
                ]);
            }
        }

        return $overdueInvoices->count();
    }

    // ── Send WA reminders for unpaid invoices ─────────────────
    public static function sendReminders(int $daysBeforeDue = 3): int
    {
        $targetDate = now()->addDays($daysBeforeDue)->toDateString();

        $invoices = Invoice::with(['student.user'])
            ->where('status', 'unpaid')
            ->whereDate('due_date', $targetDate)
            ->get();

        $wa   = new WhatsappService();
        $sent = 0;

        foreach ($invoices as $invoice) {
            $phone = $invoice->student->parent_phone ?? '';
            if (!$phone) continue;

            $sent += $wa->sendInvoiceReminder(
                phone      : $phone,
                studentName: $invoice->student->user->name,
                period     : $invoice->month_name . ' ' . $invoice->year,
                amount     : currency($invoice->amount),
                dueDate    : $invoice->due_date->format('d M Y'),
            ) ? 1 : 0;
        }

        return $sent;
    }

    // ── Generate sequential invoice number ────────────────────
    public static function generateNumber(): string
    {
        $prefix = setting('invoice_prefix', 'INV');
        $year   = date('Y');
        $count  = Invoice::whereYear('created_at', $year)->count() + 1;

        return "{$prefix}-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
