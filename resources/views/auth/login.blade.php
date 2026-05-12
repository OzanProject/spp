@extends('layouts.auth')

@section('title', 'Login')

@section('content')
{{-- Brand Logo --}}
<a href="#" class="brand-logo">
    @if(setting('school_logo'))
        <img src="{{ school_logo() }}" alt="Logo" style="height: 40px; margin-right: 10px;">
    @else
        <div class="logo-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
    @endif
    <span>{{ setting('school_name', 'Sistem SPP') }}</span>
</a>

<h2 class="auth-title">Selamat Datang 👋</h2>
<p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan.</p>

{{-- Alert Error --}}
@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" style="border-radius:10px; font-size:14px;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <div>{{ $errors->first() }}</div>
    </div>
@endif

{{-- Form --}}
<form action="{{ route('login') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group-icon">
            <i class="bi bi-envelope icon"></i>
            <input
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="contoh@email.com"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
            >
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group-icon">
            <i class="bi bi-lock icon"></i>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Masukkan password"
                autocomplete="current-password"
            >
            <i class="bi bi-eye toggle-pw"
               onclick="togglePw()"
               style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; font-size:18px; z-index:5;"></i>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember" style="font-size:13px; color:#6b7280;">
                Ingat saya
            </label>
        </div>
        <a href="{{ route('password.request') }}" style="font-size:13px; color:#6366f1; text-decoration:none; font-weight:500;">Lupa password?</a>
    </div>

    <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
    </button>
</form>

<p class="text-center mt-4 mb-0" style="font-size:14px; color:#6b7280;">
    Belum punya akun?
    <a href="{{ route('register') }}" style="color:#6366f1; font-weight:600; text-decoration:none;">Daftar sebagai siswa</a>
</p>


@endsection

@section('scripts')
<script>
function togglePw() {
    const pw  = document.getElementById('password');
    const ico = document.querySelector('.toggle-pw');
    if (pw.type === 'password') {
        pw.type = 'text';
        ico.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pw.type = 'password';
        ico.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection
