<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        // Handle success redirect from Midtrans Snap
        if ($request->has('success') || $request->has('order_id')) {
            session()->flash('success', 'Pembayaran berhasil! Status akan diperbarui secara otomatis.');
        }

        $payments = Payment::whereHas('invoice', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
            ->with('invoice')
            ->latest()
            ->paginate(10);

        return view('siswa.payments.index', compact('payments'));
    }

    public function verify(Request $request)
    {
        $orderId = $request->order_id;

        if (!$orderId) {
            return response()->json(['status' => false]);
        }

        \Midtrans\Config::$serverKey = setting('midtrans_server_key');
        \Midtrans\Config::$isProduction = setting('midtrans_is_production') == '1';

        try {
            \Illuminate\Support\Facades\Log::info('Verifying Order ID: ' . $orderId);

            $status = \Midtrans\Transaction::status($orderId);

            // Log as array for Laravel Log
            \Illuminate\Support\Facades\Log::info('Midtrans Verification Success', (array) $status);

            // Parse Invoice ID
            $parts = explode('-', $orderId);
            $invoiceId = $parts[1] ?? null;
            $invoice = Invoice::find($invoiceId);

            // Aman untuk object maupun array
            $transactionStatus = is_array($status)
                ? ($status['transaction_status'] ?? null)
                : ($status->transaction_status ?? null);

            if ($invoice && ($transactionStatus == 'settlement' || $transactionStatus == 'capture')) {
                $this->markAsPaid($invoice, is_array($status) ? (object) $status : $status);

                return response()->json(['status' => true]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Status saat ini: ' . ($transactionStatus ?? 'Unknown')
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Midtrans Verify Warning for ID ' . $orderId . ': ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Transaksi belum ditemukan atau belum diproses Midtrans.'
            ]);
        }
    }

    public function create(Request $request)
    {
        $student = Auth::user()->student;
        $invoiceId = $request->input('invoice_id');

        $invoices = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get();

        $selectedInvoice = null;

        if ($invoiceId) {
            $selectedInvoice = Invoice::where('id', $invoiceId)
                ->where('student_id', $student->id)
                ->where('status', '!=', 'paid')
                ->first();
        }

        $gateway = [
            'provider' => setting('payment_gateway_provider', 'none'),
            'active' => false,
            'client_key' => '',
        ];

        if ($gateway['provider'] === 'midtrans' && setting('midtrans_active')) {
            $gateway['active'] = true;
            $gateway['client_key'] = setting('midtrans_client_key');
            $gateway['snap_url'] = setting('midtrans_is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
        }

        return view('siswa.payments.create', compact('invoices', 'selectedInvoice', 'gateway'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bank_name' => 'required|string|max:50',
            'sender_name' => 'required|string|max:100',
            'paid_at' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Ensure own invoice
        if ($invoice->student_id !== Auth::user()->student->id) {
            abort(403);
        }

        // Check if already paid or pending
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $filename = time() . '_proof_' . Auth::user()->id . '.' . $request->file('proof')->getClientOriginalExtension();

        $request->file('proof')->storeAs('proofs', $filename, 'public');

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount,
            'method' => 'transfer',
            'bank_name' => $request->bank_name,
            'sender_name' => $request->sender_name,
            'proof' => $filename,
            'status' => 'pending',
            'paid_at' => $request->paid_at,
            'note' => $request->note,
        ]);

        $invoice->update(['status' => 'pending']);

        return redirect()->route('siswa.payments.index')->with('success', 'Bukti pembayaran berhasil diupload. Mohon tunggu verifikasi admin.');
    }

    public function getSnapToken(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        // Gunakan perbandingan longgar (!=) untuk menghindari masalah tipe data string vs int
        if ($invoice->student_id != Auth::user()->student->id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak. Tagihan ini bukan milik Anda (User ID: ' . Auth::user()->id . ', Student ID: ' . Auth::user()->student->id . ', Invoice Student ID: ' . $invoice->student_id . ')'
            ]);
        }

        // Configure Midtrans
        \Midtrans\Config::$serverKey = setting('midtrans_server_key');
        \Midtrans\Config::$isProduction = setting('midtrans_is_production') == '1';
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . $invoice->id . '-' . time(),
                'gross_amount' => (int) $invoice->amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->student->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'SPP-' . $invoice->id,
                    'price' => (int) $invoice->amount,
                    'quantity' => 1,
                    'name' => 'SPP ' . $invoice->month_name . ' ' . $invoice->year,
                ]
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return response()->json([
                'status' => true,
                'token' => $snapToken
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->all();

        \Illuminate\Support\Facades\Log::info('Midtrans Webhook Received', $payload);

        \Midtrans\Config::$serverKey = setting('midtrans_server_key');
        \Midtrans\Config::$isProduction = setting('midtrans_is_production') == '1';

        try {
            /*
            |--------------------------------------------------------------------------
            | Perbaikan error tail:
            |--------------------------------------------------------------------------
            | Tidak semua webhook Midtrans adalah payment notification.
            | Ada account_linked, subscription, dan payload lain yang tidak membawa:
            | - order_id
            | - transaction_status
            | - transaction_id
            |
            | Jadi payload seperti itu harus diabaikan dengan response 200.
            */
            if (
                !isset($payload['order_id']) ||
                !isset($payload['transaction_status'])
            ) {
                \Illuminate\Support\Facades\Log::warning('Midtrans Webhook Ignored: Missing required payment fields', [
                    'available_keys' => array_keys($payload),
                ]);

                return response()->json([
                    'message' => 'Ignored: not a payment notification'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Handle Test Notification dari Dashboard Midtrans
            |--------------------------------------------------------------------------
            */
            if (str_contains($payload['order_id'], 'payment_notif_test')) {
                \Illuminate\Support\Facades\Log::info('Midtrans Test Notification Ignored', [
                    'order_id' => $payload['order_id'],
                ]);

                return response()->json([
                    'message' => 'Test OK'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil data dengan aman
            |--------------------------------------------------------------------------
            */
            $orderId = $payload['order_id'];
            $transactionStatus = $payload['transaction_status'];
            $type = $payload['payment_type'] ?? null;
            $fraud = $payload['fraud_status'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Parse Invoice ID
            |--------------------------------------------------------------------------
            | Format order_id:
            | INV-{invoice_id}-{time}
            */
            $parts = explode('-', $orderId);

            if (count($parts) < 2 || $parts[0] !== 'INV') {
                \Illuminate\Support\Facades\Log::warning('Midtrans Webhook Ignored: Invalid Order ID Format', [
                    'order_id' => $orderId,
                ]);

                return response()->json([
                    'message' => 'Invalid Format'
                ], 200);
            }

            $invoiceId = $parts[1];
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                \Illuminate\Support\Facades\Log::warning('Invoice not found for Order ID: ' . $orderId);

                return response()->json([
                    'message' => 'Not Found'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Update status berdasarkan response Midtrans
            |--------------------------------------------------------------------------
            | Logika utama tetap sama.
            */
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                if ($transactionStatus == 'capture' && $type == 'credit_card' && $fraud == 'challenge') {
                    // Masih dalam tantangan/challenge
                } else {
                    // LUNAS
                    $this->markAsPaid($invoice, (object) $payload);
                }
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // GAGAL
                $invoice->update(['status' => 'unpaid']);

                if ($invoice->payment) {
                    $invoice->payment->update(['status' => 'failed']);
                }
            }

            return response()->json([
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Webhook Error: ' . $e->getMessage(), [
                'available_keys' => array_keys($payload),
            ]);

            return response()->json([
                'message' => 'Error processed'
            ], 200);
        }
    }

    private function markAsPaid($invoice, $notif)
    {
        if ($invoice->status === 'paid') {
            return;
        }

        // Update Invoice
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Create Payment Record
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount,
            'paid_at' => now(),
            'method' => 'gateway',
            'status' => 'success',
            'bank_name' => $notif->payment_type ?? 'Gateway',
            'sender_name' => 'Automatic Payment',
            'note' => 'Paid via Midtrans Gateway. ID: ' . ($notif->transaction_id ?? '-'),
        ]);

        // Send Notification (Hanya jika data siswa lengkap)
        if ($invoice && $invoice->student && $invoice->student->user) {
            try {
                $invoice->student->user->notify(new \App\Notifications\PaymentSuccessful($invoice, $payment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Notification Error: ' . $e->getMessage());
            }

            // Send WhatsApp Notification
            if ($invoice->student->parent_phone) {
                try {
                    $msg = "*PEMBAYARAN BERHASIL*\n\n";
                    $msg .= "Halo " . $invoice->student->user->name . ",\n";
                    $msg .= "Pembayaran SPP Anda untuk periode *" . $invoice->month_name . " " . $invoice->year . "* sebesar *" . currency($invoice->amount) . "* telah kami terima dan diverifikasi.\n\n";
                    $msg .= "Terima kasih telah melakukan pembayaran tepat waktu.\n";
                    $msg .= "_" . setting('school_name') . "_";

                    \App\Services\WhatsappService::send($invoice->student->parent_phone, $msg);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('WA Notification Error: ' . $e->getMessage());
                }
            }
        }
    }
}