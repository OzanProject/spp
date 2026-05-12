<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Auto-seed any missing keys on first visit
        Setting::seedDefaults();

        $groups   = Setting::GROUPS;
        $settings = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('backend.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Handle file uploads explicitly
        $fileKeys = ['school_logo', 'favicon'];
        
        foreach ($fileKeys as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $request->validate([
                    $key => 'image|mimes:png,jpg,jpeg,svg|max:2048'
                ]);

                // Hapus file lama jika ada
                $oldValue = Setting::get($key);
                if ($oldValue && Storage::disk('public')->exists("logos/{$oldValue}")) {
                    Storage::disk('public')->delete("logos/{$oldValue}");
                }

                $filename = time() . "_{$key}." . $file->getClientOriginalExtension();
                $file->storeAs('logos', $filename, 'public');
                $data[$key] = $filename; // override input file dengan nama file
            } else {
                // Jangan override value jika tidak ada file yang diunggah
                unset($data[$key]);
            }
        }

        foreach ($data as $key => $value) {
            $existing = Setting::where('key', $key)->first();
            if ($existing) {
                $existing->update(['value' => is_string($value) ? trim($value) : ($value ?? '')]);
                Cache::forget("setting_{$key}");
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil disimpan.'
            ]);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
    public function testWa()
    {
        $token  = setting('wa_token');
        $sender = setting('wa_sender');

        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Token belum diisi.']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/device');

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                return response()->json([
                    'status' => true, 
                    'message' => 'Koneksi Berhasil! Device: ' . ($data['name'] ?? 'Aktif')
                ]);
            }

            return response()->json([
                'status' => false, 
                'message' => $data['reason'] ?? 'Gagal terhubung ke Fonnte.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function testMidtrans()
    {
        $serverKey = setting('midtrans_server_key');
        $isProduction = setting('midtrans_is_production') == '1';

        if (!$serverKey) {
            return response()->json(['status' => false, 'message' => 'Server Key belum diisi.']);
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'TEST-CONN-' . time(),
                    'gross_amount' => 10000,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            return response()->json([
                'status' => true, 
                'message' => 'Kredensial Valid! Koneksi Berhasil.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    public function testNotification()
    {
        try {
            $student = \App\Models\Student::with('user')->first();
            if (!$student) {
                return response()->json(['status' => false, 'message' => 'Tidak ada siswa yang ditemukan untuk dikirimi tes.']);
            }

            $invoice = \App\Models\Invoice::first();
            if (!$invoice) {
                return response()->json(['status' => false, 'message' => 'Buat minimal satu tagihan untuk melakukan tes ini.']);
            }

            $payment = \App\Models\Payment::first();
            
            // Send Notification
            $student->user->notify(new \App\Notifications\PaymentSuccessful($invoice, $payment));

            return response()->json([
                'status' => true, 
                'message' => 'Notifikasi tes berhasil dikirim ke ' . $student->user->name . '. Silakan cek login sebagai siswa tersebut.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function testWaMessage()
    {
        try {
            $student = \App\Models\Student::with('user')->first();
            if (!$student || !$student->parent_phone) {
                return response()->json(['status' => false, 'message' => 'Tidak ada siswa dengan nomor HP orang tua untuk dikirimi tes.']);
            }

            $msg = "*TES NOTIFIKASI WHATSAPP*\n\n";
            $msg .= "Halo! Ini adalah pesan percobaan dari sistem *" . setting('school_name') . "*.\n";
            $msg .= "Jika Anda menerima pesan ini, berarti integrasi WhatsApp sudah berjalan dengan benar.\n\n";
            $msg .= "Waktu: " . now()->format('d/m/Y H:i:s');

            $result = \App\Services\WhatsappService::send($student->parent_phone, $msg);

            if (isset($result['status']) && $result['status'] === true) {
                return response()->json([
                    'status' => true, 
                    'message' => 'Pesan tes berhasil dikirim ke ' . $student->parent_phone . ' (' . $student->user->name . ')'
                ]);
            }

            return response()->json([
                'status' => false, 
                'message' => $result['reason'] ?? $result['message'] ?? 'Gagal mengirim pesan.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
