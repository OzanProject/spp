@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
<h2 class="auth-title">Verifikasi OTP 📲</h2>
<p class="auth-subtitle">Masukkan 6 digit kode yang dikirim ke WhatsApp Anda. Kode ini berlaku selama <strong>60 menit</strong>.</p>

<form action="{{ route('password.verify.otp.submit') }}" method="POST">
    @csrf
    <input type="hidden" name="phone" value="{{ $phone }}">

    <div class="mb-4">
        <label class="form-label text-center d-block">Kode OTP</label>
        <div class="d-flex justify-content-center gap-2">
            <input
                type="text"
                name="otp"
                class="form-control text-center fw-bold fs-4 rounded-3 @error('otp') is-invalid @enderror"
                maxlength="6"
                placeholder="000000"
                style="letter-spacing: 5px; height: 60px;"
                required
                autofocus
            >
        </div>
        @error('otp')
            <div class="text-danger text-center small mt-2">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-login mb-4">
        <i class="bi bi-shield-check me-2"></i> Verifikasi Kode
    </button>

    <div class="text-center">
        <p class="text-muted small">Tidak menerima kode? 
            <form action="{{ route('password.email') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="phone" value="{{ $phone }}">
                <button type="submit" class="btn btn-link p-0 text-primary small fw-bold text-decoration-none">Kirim ulang</button>
            </form>
        </p>
        <a href="{{ route('password.request') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Ganti Nomor
        </a>
    </div>
</form>
@endsection
