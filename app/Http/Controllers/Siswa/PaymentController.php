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

        $payments = Payment::whereHas('invoice', function($q) use ($student) {
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
        if (!$orderId) return response()->json(['status' => false]);

        \Midtrans\Config::$serverKey = setting('midtrans_server_key');
        \Midtrans\Config::$isProduction = setting('midtrans_is_production') == '1';

        try {
            $status = \Midtrans\Transaction::status($orderId);
            
            // Parse Invoice ID
            $parts = explode('-', $orderId);
            $invoiceId = $parts[1] ?? null;
            $invoice = Invoice::find($invoiceId);

            if ($invoice && ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture')) {
                $this->markAsPaid($invoice, $status);
                return response()->json(['status' => true]);
            }

            return response()->json(['status' => false, 'message' => 'Status: ' . $status->transaction_status]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        $student = Auth::user()->student;
        $invoiceId = $request->get('invoice_id');
        
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
            'active'   => false,
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
        
        if ($invoice->student_id !== Auth::user()->student->id) {
            return response()->json(['status' => false, 'message' => 'Akses ditolak.']);
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
            return response()->json(['status' => true, 'token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    public function handleNotification(Request $request)
    {
        \Midtrans\Config::$serverKey = setting('midtrans_server_key');
        \Midtrans\Config::$isProduction = setting('midtrans_is_production') == '1';

        try {
            $notif = new \Midtrans\Notification();
            
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status;

            // Parse Invoice ID from Order ID (Format: INV-{id}-{time})
            $parts = explode('-', $order_id);
            $invoiceId = $parts[1] ?? null;

            if (!$invoiceId) return response()->json(['message' => 'Invalid Order ID'], 400);

            $invoice = Invoice::find($invoiceId);
            if (!$invoice) return response()->json(['message' => 'Invoice not found'], 404);

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        // Handle challenge
                    } else {
                        $this->markAsPaid($invoice, $notif);
                    }
                }
            } else if ($transaction == 'settlement') {
                $this->markAsPaid($invoice, $notif);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function markAsPaid($invoice, $notif)
    {
        if ($invoice->status === 'paid') return;

        // Update Invoice
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Create Payment Record
        $payment = \App\Models\Payment::create([
            'invoice_id'  => $invoice->id,
            'amount'      => $invoice->amount,
            'paid_at'     => now(),
            'method'      => 'gateway',
            'status'      => 'success',
            'bank_name'   => $notif->payment_type ?? 'Gateway',
            'sender_name' => 'Automatic Payment',
            'note'        => 'Paid via Midtrans Gateway. ID: ' . $notif->transaction_id,
        ]);

        // Send Notification
        $invoice->student->user->notify(new \App\Notifications\PaymentSuccessful($invoice, $payment));

        // Send WhatsApp Notification
        if ($invoice->student->parent_phone) {
            $msg = "*PEMBAYARAN BERHASIL*\n\n";
            $msg .= "Halo " . $invoice->student->user->name . ",\n";
            $msg .= "Pembayaran SPP Anda untuk periode *" . $invoice->month_name . " " . $invoice->year . "* sebesar *" . currency($invoice->amount) . "* telah kami terima dan diverifikasi.\n\n";
            $msg .= "Terima kasih telah melakukan pembayaran tepat waktu.\n";
            $msg .= "_" . setting('school_name') . "_";
            
            \App\Services\WhatsappService::send($invoice->student->parent_phone, $msg);
        }
    }
}
