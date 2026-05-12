<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['student.user', 'student.classRoom', 'payment'])
            ->whereIn('status', ['unpaid', 'overdue', 'pending'])
            ->latest();

        if ($request->filled('class_room_id')) {
            $query->whereHas('student', fn($q) => $q->where('class_room_id', $request->class_room_id));
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $invoices = $query->paginate(15)->withQueryString();
        $classes  = \App\Models\ClassRoom::all();

        $stats = [
            'pending'  => Invoice::where('status', 'pending')->count(),
            'overdue'  => Invoice::where('status', 'overdue')->count(),
            'paid_today' => Payment::where('status', 'success')->whereDate('paid_at', today())->count(),
            'total_today' => Payment::where('payments.status', 'success')->whereDate('paid_at', today())
                ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
                ->sum('invoices.amount'),
        ];

        return view('backend.payments.index', compact('invoices', 'stats', 'classes'));
    }

    public function confirm(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment = Payment::updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'amount'      => $invoice->amount,
                'method'      => 'transfer',
                'bank_name'   => 'Manual',
                'sender_name' => 'Dikonfirmasi Admin',
                'paid_at'     => now(), 
                'status'      => 'success',
                'note'        => ($invoice->payment->note ?? '') . " | Dikonfirmasi oleh admin"
            ]
        );

        // Send Notification (hanya jika data siswa lengkap)
        if ($invoice->student && $invoice->student->user) {
            try {
                $invoice->student->user->notify(new \App\Notifications\PaymentSuccessful($invoice, $payment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Confirm Notification Error: ' . $e->getMessage());
            }

            // Send WhatsApp Notification
            if ($invoice->student->parent_phone) {
                try {
                    $msg = "*PEMBAYARAN BERHASIL*\n\n";
                    $msg .= "Halo " . $invoice->student->user->name . ",\n";
                    $msg .= "Pembayaran SPP *" . $invoice->month_name . " " . $invoice->year . "* sebesar *" . currency($invoice->amount) . "* telah DIKONFIRMASI oleh Admin.\n\n";
                    $msg .= "Status: LUNAS\n";
                    $msg .= "Terima kasih.\n";
                    $msg .= "_" . setting('school_name') . "_";
                    
                    \App\Services\WhatsappService::send($invoice->student->parent_phone, $msg);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Confirm WA Error: ' . $e->getMessage());
                }
            }
        }

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Invoice $invoice)
    {
        $invoice->update(['status' => 'unpaid']);
        
        if ($invoice->payment) {
            $invoice->payment->update([
                'status' => 'failed',
                'reject_reason' => $request->reason
            ]);

            // Send Notification (hanya jika data siswa lengkap)
            if ($invoice->student && $invoice->student->user) {
                try {
                    $invoice->student->user->notify(new \App\Notifications\PaymentRejected($invoice, $invoice->payment, $request->reason));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Reject Notification Error: ' . $e->getMessage());
                }

                // Send WhatsApp Notification
                if ($invoice->student->parent_phone) {
                    try {
                        $msg = "*PEMBAYARAN DITOLAK*\n\n";
                        $msg .= "Halo " . $invoice->student->user->name . ",\n";
                        $msg .= "Mohon maaf, pembayaran SPP *" . $invoice->month_name . " " . $invoice->year . "* Anda DITOLAK oleh Admin.\n\n";
                        $msg .= "*Alasan:* " . $request->reason . "\n\n";
                        $msg .= "Silakan hubungi bagian keuangan atau upload ulang bukti pembayaran yang benar.\n";
                        $msg .= "_" . setting('school_name') . "_";
                        
                        \App\Services\WhatsappService::send($invoice->student->parent_phone, $msg);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Reject WA Error: ' . $e->getMessage());
                    }
                }
            }
        }

        return back()->with('success', 'Pembayaran berhasil ditolak dan status dikembalikan ke belum bayar.');
    }
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu data untuk dihapus.');
        }

        \App\Models\Invoice::whereIn('id', $ids)->delete();

        return redirect()->route('admin.payments.index')->with('success', count($ids) . ' data berhasil dihapus.');
    }
}
