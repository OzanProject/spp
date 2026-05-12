<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // ── Cetak satu kwitansi pembayaran ─────────────────────────
    public function invoice(Invoice $invoice)
    {
        if (auth()->user()->role == 'student' && $invoice->student_id != auth()->user()->student->id) {
            abort(403, 'Akses ditolak.');
        }

        $invoice->load(['student.user', 'student.classRoom', 'payment']);

        $data = [
            'invoice'      => $invoice,
            'invoiceNo'    => InvoiceService::generateNumber(),
            'schoolName'   => setting('school_name', 'Sistem SPP'),
            'schoolAddress'=> setting('school_address', ''),
            'schoolPhone'  => setting('school_phone', ''),
            'schoolEmail'  => setting('school_email', ''),
            'bankName'     => setting('bank_name', ''),
            'bankAccount'  => setting('bank_account', ''),
            'bankHolder'   => setting('bank_holder', ''),
            'primaryColor' => primary_color(),
            'logoUrl'      => school_logo(),
            'timezone'     => setting('timezone', 'Asia/Jakarta'),
            'timezoneAbbr' => match(setting('timezone')) {
                'Asia/Makassar' => 'WITA',
                'Asia/Jayapura' => 'WIT',
                default => 'WIB'
            },
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data)
            ->setPaper('a5', 'portrait');

        $filename = 'Kwitansi-' . $invoice->student->nis . '-' . $invoice->month_name . $invoice->year . '.pdf';

        return $pdf->stream($filename);
    }

    // ── Cetak laporan bulanan ───────────────────────────────────
    public function report(int $year, int $month)
    {
        $invoices = Invoice::with(['student.user', 'student.classRoom'])
            ->where('year', $year)->where('month', $month)
            ->orderBy('status')->get();

        $totalPaid    = $invoices->where('status', 'paid')->sum('amount');
        $totalUnpaid  = $invoices->where('status', 'unpaid')->count();
        $totalOverdue = $invoices->where('status', 'overdue')->count();

        $data = [
            'invoices'     => $invoices,
            'year'         => $year,
            'month'        => $month,
            'monthName'    => \App\Models\Invoice::MONTHS[$month] ?? '-',
            'totalPaid'    => $totalPaid,
            'totalUnpaid'  => $totalUnpaid,
            'totalOverdue' => $totalOverdue,
            'schoolName'   => setting('school_name', 'Sistem SPP'),
            'schoolAddress'=> setting('school_address', ''),
            'schoolPhone'  => setting('school_phone', ''),
            'primaryColor' => primary_color(),
            'academicYear' => setting('academic_year', ''),
            'timezoneAbbr' => match(setting('timezone')) {
                'Asia/Makassar' => 'WITA',
                'Asia/Jayapura' => 'WIT',
                default => 'WIB'
            },
        ];

        $pdf = Pdf::loadView('pdf.report', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan-SPP-{$year}-{$month}.pdf");
    }
}
