<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/siswa/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // ─── Register ──────────────────────────────────────────────

    public function showRegister()
    {
        $classes = ClassRoom::orderBy('name')->get();
        return view('auth.register', compact('classes'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'nis'           => 'required|string|max:20|unique:students,nis',
            'class_room_id' => 'required|exists:class_rooms,id',
            'parent_phone'  => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
        ], [
            'name.required'          => 'Nama lengkap wajib diisi.',
            'nis.required'           => 'NIS wajib diisi.',
            'nis.unique'             => 'NIS sudah terdaftar, hubungi admin.',
            'class_room_id.required' => 'Kelas wajib dipilih.',
            'parent_phone.required'  => 'Nomor WhatsApp wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email sudah digunakan.',
            'password.min'           => 'Password minimal 8 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
        ]);

        // Buat user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'student',
        ]);

        // Buat data siswa
        Student::create([
            'user_id'       => $user->id,
            'class_room_id' => $request->class_room_id,
            'nis'           => $request->nis,
            'parent_phone'  => $request->parent_phone,
        ]);

        // Kirim Notifikasi WhatsApp
        try {
            $schoolName = setting('school_name', 'Sekolah');
            $message = "*Registrasi Berhasil!* 📝\n\n"
                     . "Halo, *{$request->name}*\n"
                     . "Akun Anda di Portal SPP {$schoolName} telah berhasil dibuat.\n\n"
                     . "Detail Akun:\n"
                     . "- Email: {$request->email}\n"
                     . "- NIS: {$request->nis}\n\n"
                     . "Silakan login untuk memantau tagihan dan pembayaran SPP Anda.\n"
                     . "Terima kasih.";
            
            \App\Services\WhatsappService::send($request->parent_phone, $message);
        } catch (\Exception $e) {
            // Silently fail if WA service is down
        }

        // Auto login setelah register
        Auth::login($user);

        return redirect('/siswa/dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '.');
    }

    // ─── Forgot Password ────────────────────────────────────────

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        // Cari siswa berdasarkan nomor HP ortu
        $student = Student::where('parent_phone', $request->phone)->first();

        if (!$student) {
            return back()->with('error', 'Nomor WhatsApp tidak terdaftar.');
        }

        $user = $student->user;

        if (!$user) {
            return back()->with('error', 'Data pengguna tidak valid.');
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(60), // Diperpanjang ke 60 menit
        ]);

        // Send WhatsApp
        $schoolName = setting('school_name', 'Portal SPP');
        $message = "*KODE OTP LUPA PASSWORD* 🔒\n\n"
                 . "Halo, *{$user->name}*\n"
                 . "Kode OTP Anda adalah: *{$otp}*\n\n"
                 . "Gunakan kode ini untuk meriset password Anda. Kode berlaku selama 60 menit.\n"
                 . "Jangan berikan kode ini kepada siapa pun.\n\n"
                 . "Terima kasih, {$schoolName}";

        \App\Services\WhatsappService::send($request->phone, $message);

        return redirect()->route('password.verify.otp', ['phone' => $request->phone])
            ->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    public function showVerifyOtp(Request $request)
    {
        $phone = $request->phone;
        return view('auth.verify-otp', compact('phone'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string|size:6',
        ]);

        $otp = trim($request->otp);

        // Cari User yang memiliki OTP tersebut dan terhubung dengan nomor HP tersebut
        $user = User::where('otp', $otp)
            ->whereHas('student', function($q) use ($request) {
                $q->where('parent_phone', $request->phone);
            })
            ->first();

        if (!$user) {
            // Cek apakah nomor HP ada tapi OTP nya memang salah untuk semua akun terkait nomor itu
            $phoneExists = Student::where('parent_phone', $request->phone)->exists();
            if ($phoneExists) {
                return back()->with('error', 'Kode OTP yang Anda masukkan salah.');
            }
            return back()->with('error', 'Data nomor WhatsApp tidak ditemukan.');
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.');
        }

        return redirect()->route('password.reset', ['phone' => $request->phone, 'token' => $otp]);
    }

    public function showResetPassword(Request $request)
    {
        $phone = $request->phone;
        $token = $request->token;
        return view('auth.reset-password', compact('phone', 'token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'token'    => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cari User yang memiliki token tersebut dan terhubung dengan nomor HP tersebut
        $user = User::where('otp', $request->token)
            ->whereHas('student', function($q) use ($request) {
                $q->where('parent_phone', $request->phone);
            })
            ->first();

        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Sesi tidak valid atau sudah kedaluwarsa.');
        }

        $user->update([
            'password'       => Hash::make($request->password),
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
    }

    // ─── Logout ────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
