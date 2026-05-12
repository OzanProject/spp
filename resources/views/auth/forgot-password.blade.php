@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
{{-- Brand Logo --}}
<a href="{{ route('login') }}" class="brand-logo">
    @if(setting('school_logo'))
        <img src="{{ school_logo() }}" alt="Logo" style="height: 40px; margin-right: 10px;">
    @else
        <div class="logo-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
    @endif
    <span>{{ setting('school_name', 'Sistem SPP') }}</span>
</a>

<h2 class="auth-title">Lupa Password? 🔒</h2>
<p class="auth-subtitle">Masukkan nomor WhatsApp Anda untuk menerima kode OTP.</p>

<form action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label class="form-label">Nomor WhatsApp Terdaftar</label>
        <div class="input-group-icon">
            <i class="bi bi-whatsapp icon"></i>
            <input
                type="text"
                name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                placeholder="Contoh: 0812xxxxxxxx"
                value="{{ old('phone') }}"
                required
                autofocus
            >
        </div>
        @error('phone')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-login mb-3">
        <i class="bi bi-whatsapp me-2"></i> Kirim Kode OTP
    </button>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
        </a>
    </div>
</form>
@endsection
